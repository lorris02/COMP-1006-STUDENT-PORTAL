"""Exercise the public demo through actual HTTP requests."""
import http.cookiejar
import re
import sys
import urllib.error
import urllib.parse
import urllib.request

base = (sys.argv[1] if len(sys.argv) > 1 else "http://localhost:8080").rstrip("/")
checks = 0

def client():
    return urllib.request.build_opener(
        urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar())
    )

browser = client()

def request(path, data=None, connection=None):
    connection = connection or browser
    encoded = urllib.parse.urlencode(data).encode() if data is not None else None
    try:
        with connection.open(base + path, data=encoded) as response:
            return response.status, response.read().decode(), response.headers
    except urllib.error.HTTPError as error:
        return error.code, error.read().decode(), error.headers

def check(condition, label):
    global checks
    assert condition, label
    checks += 1

def token(page):
    return re.search(r'name="csrf" value="([^"]+)"', page).group(1)

status, page, headers = request("/view.php")
check(status == 200 and "Alex Morgan" in page, "demo loads")
check("frame-ancestors 'none'" in headers["Content-Security-Policy"], "security headers")
csrf = token(page)
check(request("/add.php")[0] == 405, "GET cannot add")
check(request("/delete.php")[0] == 405, "GET cannot delete")
check(request("/reset.php")[0] == 405, "GET cannot reset")
data = dict(name="<script>alert(1)</script>", student_id="00042", age="22",
            gender="female", grade="0", csrf=csrf, original_id="")
check(request("/add.php", {**data, "csrf": "wrong"})[0] == 403, "reject bad CSRF")
status, page, _ = request("/add.php", data)
check(status == 200 and "00042" in page, "create through form")
check("&lt;script&gt;alert(1)&lt;/script&gt;" in page and "<script>" not in page, "escape stored markup")
check("0%" in page, "zero grade displays")
status, page, _ = request("/add.php", data)
check("already in use" in page, "duplicate rejected")
bad = {**data, "student_id": "00099", "grade": "101"}
check("Enter a grade" in request("/add.php", bad)[1], "invalid grade rejected")
check("00099" not in request("/view.php")[1], "invalid record never inserted")
edited = {**data, "name": "Test Student", "grade": "92.5", "original_id": "00042"}
check("Test Student" in request("/add.php", edited)[1], "edit works")
check("Test Student" in request("/view.php?q=00042")[1], "ID search works")
check("No matching students" in request("/view.php?q=missing")[1], "empty search state")
check("00042" not in request("/view.php", connection=client())[1], "sessions isolated")
status, page, _ = request("/delete.php", {"csrf": csrf, "id": "00042"})
check("Delete this student?" in page, "confirmation displayed")
check("00042" in request("/view.php")[1], "confirmation does not delete yet")
status, page, _ = request("/delete.php", {"csrf": csrf, "id": "00042", "confirm": "yes"})
check("00042" not in page, "confirmed deletion works")
request("/delete.php", {"csrf": csrf, "id": "100001", "confirm": "yes"})
check("Alex Morgan" in request("/reset.php", {"csrf": csrf})[1], "reset restores samples")
print(f"{checks} HTTP checks passed.")

import urllib.request
import urllib.error

try:
    response = urllib.request.urlopen('http://127.0.0.1:8000/')
    print('Status:', response.status)
    print('Response (first 500 chars):')
    print(response.read()[:500].decode())
except urllib.error.URLError as e:
    print('Error:', e.reason)
except Exception as e:
    print('Error:', str(e))

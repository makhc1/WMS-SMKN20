import os

BASE_URL = os.getenv("TEST_BASE_URL", "http://127.0.0.1:8000")
HEADLESS = os.getenv("TEST_HEADLESS", "true").lower() in ("true", "1", "yes")
SLOW_MO = int(os.getenv("TEST_SLOWMO", "0"))
SCREENSHOT_DIR = os.path.join(os.path.dirname(__file__), "screenshots")

os.makedirs(SCREENSHOT_DIR, exist_ok=True)

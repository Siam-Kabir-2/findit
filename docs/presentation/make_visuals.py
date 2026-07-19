"""Generate simple visual cards for folder structure and Oracle SQL files."""
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

OUT = Path(__file__).resolve().parent / "screenshots"
OUT.mkdir(exist_ok=True)

W, H = 1400, 900
BG = (255, 255, 255)
INK = (15, 23, 42)
MUTED = (71, 85, 105)
ACCENT = (30, 64, 175)  # dark blue
LINE = (226, 232, 240)
CARD = (248, 250, 252)


def font(size, bold=False):
    candidates = [
        r"C:\Windows\Fonts\segoeuib.ttf" if bold else r"C:\Windows\Fonts\segoeui.ttf",
        r"C:\Windows\Fonts\arialbd.ttf" if bold else r"C:\Windows\Fonts\arial.ttf",
        r"C:\Windows\Fonts\calibrib.ttf" if bold else r"C:\Windows\Fonts\calibri.ttf",
    ]
    for c in candidates:
        if Path(c).exists():
            return ImageFont.truetype(c, size)
    return ImageFont.load_default()


def rounded_rect(draw, xy, fill, outline=LINE, radius=14):
    draw.rounded_rectangle(xy, radius=radius, fill=fill, outline=outline, width=2)


def make_folder():
    img = Image.new("RGB", (W, H), BG)
    d = ImageDraw.Draw(img)
    d.text((48, 36), "Project Folder Structure", font=font(36, True), fill=INK)
    d.rectangle((48, 88, 220, 94), fill=ACCENT)

    tree = [
        ("findit/", True),
        ("  app/", True),
        ("    Http/Controllers/   User + Admin controllers", False),
        ("    Middleware/         Auth guards", False),
        ("    Models/             Oracle table models", False),
        ("    Services/           FinditPlsqlService", False),
        ("    Database/           Custom PDO_OCI driver", False),
        ("  config/               database.php, auth.php", False),
        ("  database/oracle/      SQL + PL/SQL scripts", False),
        ("  resources/views/      Blade UI", False),
        ("  routes/web.php        Application routes", False),
        ("  public/               Public assets", False),
        ("  storage/app/public/   Item images", False),
    ]

    y = 130
    for line, strong in tree:
        d.text((72, y), line, font=font(22, strong), fill=INK if strong else MUTED)
        y += 48

    img.save(OUT / "31-folder-structure.png")
    print("saved 31-folder-structure.png")


def make_oracle():
    img = Image.new("RGB", (W, H), BG)
    d = ImageDraw.Draw(img)
    d.text((48, 36), "Oracle SQL Implementation", font=font(36, True), fill=INK)
    d.rectangle((48, 88, 220, 94), fill=ACCENT)

    files = [
        ("01_create_user_schema.sql", "Create findit user and grants"),
        ("02_create_tables.sql", "Tables, PKs, FKs, checks"),
        ("03_insert_sample_data.sql", "Demo users, items, claims"),
        ("04_basic_queries.sql", "Reference SELECT queries"),
        ("05_plsql_triggers_package.sql", "Sequences, triggers, findit_pkg"),
    ]

    y = 130
    for name, desc in files:
        rounded_rect(d, (48, y, W - 48, y + 100), CARD)
        d.rectangle((48, y, 58, y + 100), fill=ACCENT)
        d.text((80, y + 22), name, font=font(24, True), fill=INK)
        d.text((80, y + 58), desc, font=font(18), fill=MUTED)
        y += 120

    img.save(OUT / "32-oracle-sql-files.png")
    print("saved 32-oracle-sql-files.png")


def make_tables():
    img = Image.new("RGB", (W, H), BG)
    d = ImageDraw.Draw(img)
    d.text((48, 36), "Oracle Tables", font=font(36, True), fill=INK)
    d.rectangle((48, 88, 220, 94), fill=ACCENT)

    tables = [
        "USERS", "ADMINS", "CATEGORIES", "LOCATIONS",
        "ITEMS", "CLAIMS", "AUDIT_LOGS",
    ]
    cols = 3
    box_w, box_h = 380, 120
    start_x, start_y = 60, 140
    gap = 30
    for i, t in enumerate(tables):
        r, c = divmod(i, cols)
        x = start_x + c * (box_w + gap)
        y = start_y + r * (box_h + gap)
        rounded_rect(d, (x, y, x + box_w, y + box_h), CARD)
        d.text((x + 28, y + 42), t, font=font(28, True), fill=ACCENT)

    img.save(OUT / "33-oracle-tables.png")
    print("saved 33-oracle-tables.png")


def make_pkg():
    img = Image.new("RGB", (W, H), BG)
    d = ImageDraw.Draw(img)
    d.text((48, 36), "PL/SQL Package — FINDIT_PKG", font=font(34, True), fill=INK)
    d.rectangle((48, 88, 220, 94), fill=ACCENT)

    left = [
        "register_user", "add_item", "update_item_status",
        "submit_claim", "approve_claim", "reject_claim",
        "add_category", "add_location",
        "delete_category", "delete_location",
        "delete_user", "delete_item",
    ]
    right = [
        "get_total_users", "get_total_items",
        "get_pending_claims", "get_approved_claims",
        "get_lost_items", "get_found_items",
    ]

    d.text((72, 130), "Procedures", font=font(24, True), fill=ACCENT)
    d.text((760, 130), "Functions", font=font(24, True), fill=ACCENT)

    y = 180
    for p in left:
        d.text((72, y), "•  " + p, font=font(20), fill=INK)
        y += 42

    y = 180
    for f in right:
        d.text((760, y), "•  " + f, font=font(20), fill=INK)
        y += 42

    img.save(OUT / "34-plsql-package.png")
    print("saved 34-plsql-package.png")


if __name__ == "__main__":
    make_folder()
    make_oracle()
    make_tables()
    make_pkg()

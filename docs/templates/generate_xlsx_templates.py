from pathlib import Path

import csv

try:
    import openpyxl
except ImportError:
    raise SystemExit(
        "openpyxl is required. Install with: pip install openpyxl"
    )

TEMPLATES = [
    "voters_import_template.csv",
    "votercheck_import_template.csv",
    "contractor_voters_import_template.csv",
]


def csv_to_xlsx(csv_path: Path) -> Path:
    workbook = openpyxl.Workbook()
    sheet = workbook.active

    with csv_path.open("r", newline="", encoding="utf-8") as handle:
        reader = csv.reader(handle)
        for row in reader:
            sheet.append(row)

    xlsx_path = csv_path.with_suffix(".xlsx")
    workbook.save(xlsx_path)
    return xlsx_path


def main() -> None:
    base_dir = Path(__file__).resolve().parent
    for name in TEMPLATES:
        csv_path = base_dir / name
        if not csv_path.exists():
            print(f"Skipping missing template: {csv_path}")
            continue
        xlsx_path = csv_to_xlsx(csv_path)
        print(f"Generated {xlsx_path}")


if __name__ == "__main__":
    main()

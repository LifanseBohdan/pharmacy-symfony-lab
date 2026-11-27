import sys
import requests

API_URL = "http://127.0.0.1:8000/api/medicines"


def fetch_medicines(in_stock_only: bool = False) -> list[dict]:
    params = {"inStock": "1"} if in_stock_only else {}
    response = requests.get(API_URL, params=params, timeout=5)
    response.raise_for_status()
    return response.json()


def print_medicines(medicines: list[dict]) -> None:
    if not medicines:
        print("Препаратів не знайдено.")
        return

    print(f"{'ID':<4} {'Назва':<28} {'Виробник':<20} {'Ціна':>10} {'Зал.':>6} {'Рецепт':<8} {'Категорія'}")
    print("-" * 100)
    for item in medicines:
        category_name = (item.get("category") or {}).get("name", "-")
        rx = "Так" if item.get("requiresPrescription") else "Ні"
        price = f"{float(item.get('price', 0)):.2f} грн"
        print(
            f"{item.get('id', ''):<4} "
            f"{str(item.get('name', ''))[:27]:<28} "
            f"{str(item.get('manufacturer', ''))[:19]:<20} "
            f"{price:>10} "
            f"{item.get('stockQuantity', 0):>6} "
            f"{rx:<8} "
            f"{category_name}"
        )


def main() -> None:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8")

    in_stock_only = "--in-stock" in sys.argv[1:]
    try:
        medicines = fetch_medicines(in_stock_only=in_stock_only)
        print_medicines(medicines)
    except requests.RequestException as exc:
        print(f"Помилка при зверненні до API ({API_URL}): {exc}", file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()

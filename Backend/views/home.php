<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CarShop – Latest Cars</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        h1 { margin-bottom: 1rem; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 0.5rem; text-align: left; }
        th { background: #f4f4f4; }
    </style>
</head>
<body>
    <h1>Latest Cars in Stock</h1>

    <?php if (empty($cars)): ?>
        <p>No cars in stock.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>VIN</th>
                    <th>Trim</th>
                    <th>Color</th>
                    <th>Price</th>
                    <th>Mileage (km)</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($cars as $car): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$car['car_id']) ?></td>
                    <td><?= htmlspecialchars($car['vin']) ?></td>
                    <td><?= htmlspecialchars($car['trim'] ?? '') ?></td>
                    <td><?= htmlspecialchars($car['color'] ?? '') ?></td>
                    <td><?= htmlspecialchars((string)$car['price']) ?></td>
                    <td><?= htmlspecialchars((string)$car['mileage_km']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="/docs">API Documentation (Swagger)</a></p>
</body>
</html>

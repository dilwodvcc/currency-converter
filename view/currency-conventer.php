<?php
require_once 'Currency.php';

// Valyutalar va natijani tayyorlash
try {
    $currency = new Currency();
    $currencies = $currency->getCurrencies();
    if (!$currencies || !is_array($currencies)) {
        $currencies = [];
        $result = "Currency data could not be retrieved. Please try again later.";
    }
} catch (Exception $e) {
    $currencies = [];
    $result = "Error: " . $e->getMessage();
}

$result = $result ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);
    $from = htmlspecialchars($_POST['from'] ?? '');
    $to = htmlspecialchars($_POST['to'] ?? '');

    if ($amount <= 0 || empty($from) || empty($to)) {
        $result = "Please enter valid input data.";
    } else {
        try {
            $result = $currency->exchange($amount, $from, $to);
        } catch (Exception $e) {
            $result = "Conversion failed: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Converter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="view/c_style.css">
</head>
<body>
<div class="currency-section text-center pt-5 bg-primary-subtle">
    <h1>Currency Converter</h1>
    <p>Need to make an international business payment? Take a look at our live foreign exchange rates.</p>
    <div class="currency-card">
        <h3>Make fast and affordable international business payments</h3>
        <p>Send secure international business payments in various currencies, all at competitive rates with no hidden fees.</p>
        <form method="POST">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label for="amount" class="form-label visually-hidden">Amount</label>
                    <input type="number" id="amount" name="amount" class="form-control" placeholder="Amount" value="100">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="from">
                        <?php if (!empty($currencies)): ?>
                            <?php foreach ($currencies as $key => $rate): ?>
                                <option value="<?= $key ?>"><?= $key ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option disabled>No currencies available</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-1 text-center">
                    <span>⇆</span>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="to">
                        <?php if (!empty($currencies)): ?>
                            <?php foreach ($currencies as $key => $rate): ?>
                                <option value="<?= $key ?>" <?= $key === 'UZS' ? 'selected' : '' ?>><?= $key ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option disabled>No currencies available</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-primary-custom mt-3">Convert</button>
        </form>
        <?php if ($result): ?>
            <p class="mt-3 alert <?= strpos($result, 'Error') !== false ? 'alert-danger' : 'alert-success' ?>">
                <?= htmlspecialchars($result) ?>
            </p>
        <?php endif; ?>
    </div>
</div>
</div>
<div class="info-section bg-light p-5 text-center">
    <h4 class="fw-bold mb-3">Weather Forecast</h4>
    <p class="text-muted">Get real-time weather information for any location. Click below for more details.</p>
    <button class="btn btn-outline-danger btn-lg" onclick="window.open('weather/weather.php', '_blank')">More</button>
</div>
</body>
</html>

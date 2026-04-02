<?php
$bank_info = [
    'bank'    => '카카오뱅크',
    'account' => '7777-03-0806539',
    'holder'  => '김시우',
    'note'    => '입금 시 메모에 반드시 주문번호(ORD-XXXXXX)를 적어주세요!'
];

$plans = [
    '1day' => ['name'=>'1일 키', 'price'=>1000, 'days'=>1, 'label'=>'1 Day Key'],
    '30day' => ['name'=>'30일 키', 'price'=>30000, 'days'=>30, 'label'=>'30 Day Key'],
    'lifetime' => ['name'=>'영구 키', 'price'=>50000, 'days'=>999999, 'label'=>'Lifetime Key (무제한)']
];

$orders_file = __DIR__ . '/orders.json';
$keys_file   = __DIR__ . '/keys.json';

if (!file_exists($orders_file)) file_put_contents($orders_file, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
if (!file_exists($keys_file)) file_put_contents($keys_file, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

function generate_key() {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $part = '';
    for ($i = 0; $i < 12; $i++) $part .= $chars[rand(0, strlen($chars)-1)];
    return 'FREE-' . $part;
}

session_start();
$completed = $_SESSION['linkvertise_completed'] ?? false;
$linkvertise_url = "https://linkvertise.com/3039668/b1qqzRi8cJlM?o=sharing" . base64_encode("https://api-xggdaygeahub.ct.ws/index.php?return=1");

if (isset($_GET['return']) && $_GET['return'] == 1) {
    $_SESSION['linkvertise_completed'] = true;
    header("Location: index.php");
    exit;
}

if (isset($_GET['plan'])) {
    $plan_code = $_GET['plan'];
    if (isset($plans[$plan_code])) {
        $p = $plans[$plan_code];
        $order_id = 'ORD-' . strtoupper(substr(uniqid(), -8));
        $order = ['id'=>$order_id, 'plan'=>$plan_code, 'plan_name'=>$p['name'], 'amount'=>$p['price'], 'status'=>'pending', 'created'=>time(), 'key_assigned'=>null, 'expiry'=>null];
        $orders = json_decode(file_get_contents($orders_file), true);
        $orders[] = $order;
        file_put_contents($orders_file, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        ?>
        <!DOCTYPE html>
        <html lang="ko">
        <head><meta charset="UTF-8"><title>주문 완료</title>
        <style>body{background:#0a0a0a;color:#e0e0e0;font-family:sans-serif;text-align:center;padding:80px 20px;}
        .box{max-width:700px;margin:auto;background:#111;border:1px solid #00ff9d;border-radius:20px;padding:50px;}</style>
        </head>
        <body>
        <div class="box">
            <h1>✅ 주문이 접수되었습니다</h1>
            <p style="font-size:1.8rem;margin:20px 0;">주문번호: <?= $order_id ?></p>
            <p><?= $bank_info['bank'] ?> <?= $bank_info['account'] ?><br>예금주: <?= $bank_info['holder'] ?></p>
            <p style="color:#ff0;margin:30px 0;"><?= $bank_info['note'] ?><br><strong>입금 확인 후 키가 자동 발급됩니다.</strong></p>
            <a href="index.php?check=<?= $order_id ?>" style="padding:16px 40px;background:#00ff9d;color:#000;border-radius:50px;text-decoration:none;">주문 상태 확인</a>
        </div>
        </body></html>
        <?php
        exit;
    }
}

if (isset($_GET['check'])) {
    $order_id = $_GET['check'];
    $orders = json_decode(file_get_contents($orders_file), true);
    $found = null;
    foreach ($orders as $o) if ($o['id'] === $order_id) {$found = $o; break;}
    ?>
    <!DOCTYPE html>
    <html lang="ko">
    <head><meta charset="UTF-8"><title>주문 확인</title>
    <style>body{background:#0a0a0a;color:#e0e0e0;font-family:sans-serif;text-align:center;padding:80px 20px;}
    .box{max-width:700px;margin:auto;background:#111;border:1px solid #00ff9d;border-radius:20px;padding:50px;}</style>
    </head>
    <body>
    <div class="box">
        <?php if (!$found): ?>
            <h2>존재하지 않는 주문번호입니다.</h2>
        <?php elseif ($found['status'] === 'pending'): ?>
            <h2>⏳ 입금 확인 중입니다</h2>
            <p>관리자가 확인하는 대로 키가 발급됩니다.</p>
        <?php else: ?>
            <h2>✅ 키 발급 완료!</h2>
            <p>키: <strong style="font-size:1.8rem;"><?= $found['key_assigned'] ?></strong></p>
            <button onclick="navigator.clipboard.writeText('<?= $found['key_assigned'] ?>');alert('복사되었습니다!')" style="padding:14px 40px;background:#00ff9d;color:#000;border:none;border-radius:50px;">키 복사하기</button>
        <?php endif; ?>
    </div>
    </body></html>
    <?php
    exit;
}

if (isset($_GET['generate12h'])) {
    if (!$completed) {
        echo json_encode(['success'=>false, 'message'=>'Linkvertise를 먼저 완료해주세요.']);
        exit;
    }
    $key = generate_key();
    $expiry = time() + 43200;
    $keys = json_decode(file_get_contents($keys_file), true);
    $keys[$key] = ['expiry'=>$expiry, 'plan'=>'12hour', 'created'=>time(), 'type'=>'free_12h'];
    file_put_contents($keys_file, json_encode($keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    unset($_SESSION['linkvertise_completed']);
    echo json_encode(['success'=>true, 'key'=>$key]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XGGDAYGEAHUB | Roblox Script Hub</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap');
        :root {--primary:#00ff9d;--accent:#c026d3;}
        body {background:#0a0a0a;color:#e0e0e0;font-family:'Inter',sans-serif;margin:0;}
        header {background:rgba(10,10,10,0.95);padding:1.2rem 2rem;position:fixed;width:100%;z-index:100;border-bottom:1px solid #222;}
        .logo {font-family:'Space Grotesk',sans-serif;font-size:2rem;font-weight:700;background:linear-gradient(90deg,var(--primary),var(--accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
        .hero {height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;background:radial-gradient(circle,#1a1a2e,#0a0a0a);}
        .hero h1 {font-size:4.2rem;background:linear-gradient(90deg,#00ff9d,#c026d3); -webkit-background-clip:text;-webkit-text-fill-color:transparent;}
        .btn {padding:16px 40px;font-size:1.2rem;font-weight:600;border-radius:50px;text-decoration:none;transition:0.3s;}
        .btn-primary {background:var(--primary);color:#000;}
        .section {max-width:1200px;margin:0 auto;padding:100px 20px;}
        .products {display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:25px;}
        .card {background:#111;border:1px solid #222;border-radius:20px;padding:30px;}
        .card:hover {border-color:var(--primary);transform:translateY(-10px);}
        .price {font-size:2.2rem;font-weight:700;margin:15px 0;}
    </style>
</head>
<body>

<header>
    <div style="max-width:1200px;margin:auto;display:flex;justify-content:space-between;align-items:center;">
        <a href="index.php" class="logo">XGGDAYGEAHUB</a>
        <a href="#free" class="btn" style="background:#222;color:white;">12시간 무료 키</a>
    </div>
</header>

<section class="hero">
    <div>
        <h1>XGGDAYGEAHUB</h1>
        <p style="font-size:1.5rem;margin:20px 0;">Premium Roblox Rivals Script Hub</p>
        <a href="#plans" class="btn btn-primary">키 구매하기</a>
        <a href="#free" class="btn" style="background:#333;color:white;margin-left:15px;">12시간 무료 키 받기</a>
    </div>
</section>

<section id="plans" class="section">
    <h2 style="text-align:center;margin-bottom:50px;">키 구매</h2>
    <div class="products">
        <?php foreach($plans as $code => $p): ?>
        <div class="card">
            <h3><?= $p['label'] ?></h3>
            <div class="price">₩<?= number_format($p['price']) ?></div>
            <a href="index.php?plan=<?= $code ?>" class="btn btn-primary" style="display:block;text-align:center;margin-top:20px;">구매하기</a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="free" class="section" style="background:#111;">
    <h2 style="text-align:center;margin-bottom:40px;">🎁 12시간 무료 키</h2>
    <div style="max-width:700px;margin:auto;text-align:center;background:#0a0a0a;padding:50px;border-radius:20px;border:2px solid #00ff9d;">
        <?php if (!$completed): ?>
            <a href="<?= htmlspecialchars($linkvertise_url) ?>" target="_blank">
                <button class="btn" style="font-size:1.4rem;padding:20px 70px;">Linkvertise 광고 보고 12시간 키 받기</button>
            </a>
        <?php else: ?>
            <p style="color:#00ff9d;font-size:1.4rem;">✅ 광고 완료!</p>
            <button class="btn" onclick="generate12h()" style="font-size:1.4rem;padding:20px 70px;">12시간 키 생성하기</button>
            <div id="keyResult" style="margin-top:30px;font-size:1.5rem;display:none;"></div>
        <?php endif; ?>
    </div>
</section>

<script>
async function generate12h() {
    const btn = document.querySelector('button[onclick="generate12h()"]');
    btn.disabled = true;
    btn.textContent = "생성 중...";
    try {
        const res = await fetch('index.php?generate12h=1');
        const data = await res.json();
        if (data.success) {
            document.getElementById('keyResult').innerHTML = `키: <strong>${data.key}</strong><br><small style="color:#0f0;">loadstring(game:HttpGet("https://api-xggdaygeahub.ct.ws/getscript.php?key=${data.key}"))()</small>`;
            document.getElementById('keyResult').style.display = 'block';
        } else alert(data.message);
    } catch(e) { alert("오류 발생"); }
}
</script>

</body>
</html>

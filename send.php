<?php
declare(strict_types=1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html#kontakt");
    exit;
}

// Zieladresse
$to = "info@hjms.ch";
$subject = "Kontaktformular – hjms.ch";

// Eingaben
$first = trim($_POST["first"] ?? "");
$last  = trim($_POST["last"] ?? "");
$email = trim($_POST["email"] ?? "");
$msg   = trim($_POST["message"] ?? "");

// Honeypot (wenn ausgefüllt -> Bot)
$honeypot = trim($_POST["website"] ?? "");
if ($honeypot !== "") {
    header("Location: index.html#kontakt");
    exit;
}

// Validierung
if ($first === "" || $last === "" || $email === "" || $msg === "") {
    header("Location: index.html#kontakt");
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: index.html#kontakt");
    exit;
}

// Nachricht
$body  = "Neue Kontaktanfrage über die Website\n\n";
$body .= "Name: {$first} {$last}\n";
$body .= "E-Mail: {$email}\n\n";
$body .= "Mitteilung:\n{$msg}\n";

// WICHTIG für Zustellbarkeit: From sollte auf eigener Domain sein
$fromAddress = "info@hjms.ch";
$fromName = "hjms.ch Website";

$headers = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";
$headers[] = "From: {$fromName} <{$fromAddress}>";
$headers[] = "Reply-To: {$email}";
$headers[] = "X-Content-Type-Options: nosniff";

$ok = mail($to, $subject, $body, implode("\r\n", $headers));

if ($ok) {
    header("Location: index.html?sent=1#kontakt");
} else {
    header("Location: index.html?sent=0#kontakt");
}
exit;

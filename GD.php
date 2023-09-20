<?php
if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $uploaded_image = $_FILES['image']['tmp_name'];

    $title = $_POST['title'];
    $description = $_POST['description'];

    $image = imagecreatetruecolor(800, 600);

    $user_image = imagecreatefromjpeg($uploaded_image);

    imagecopyresampled($image, $user_image, 0, 0, 0, 0, 800, 600, imagesx($user_image), imagesy($user_image));

    $text_color = imagecolorallocate($image, 255, 255, 255);
    $font = 'fonts/Roboto-Regular.ttf';

    imagettftext($image, 24, 0, 20, 50, $text_color, $font, $title);

    imagettftext($image, 18, 0, 20, 100, $text_color, $font, $description);

    header('Content-Type: image/jpeg');

    imagejpeg($image, 'generated_image.jpg');

    imagedestroy($image);
    imagedestroy($user_image);

    header('Content-Disposition: attachment; filename="generated_image.jpg"');
    readfile('generated_image.jpg');
    exit;
} else {
    echo "Ошибка при загрузке изображения.";
}
?>

<?php
/**
 * =========================================================================
 * 
 *                      XingHan-Team 官网程序 
 * =========================================================================
 * 
 * @package     XingHan-Team Official Website
 * @author      XingHan Development Team
 * @copyright   Copyright (c) 2024, XingHan-Team
 * @link        https://www.ococn.cn
 * @since       Version 1.0.0
 * @filesource  By 奉天
 * 
 * =========================================================================
 * 
 * XingHan-Team 星涵网络工作室官方网站管理系统
 * 版权所有 (C) 2024 XingHan-Team。保留所有权利。
 * 
 * 本软件受著作权法和国际公约的保护。未经授权，不得以任何形式或方式复制、分发、
 * 传播、展示、执行、复制、发行、或以其他方式使用本软件。
 * 
 * 感谢您选择 XingHan-Team 的产品。如有任何问题或建议，请联系我们。
 * 
 * =========================================================================
 */
session_start();
$width = 150;  // 增加宽度
$height = 50;  // 增加高度
$chars = 4;    // 验证码长度
$font_path = '../assets/ttf/arial.ttf';  
$font_size = 24;  // 字体大小(像素)
$noise_lines = 5;  // 干扰线数量
$noise_points = 50;  // 干扰点数量

function generateCaptchaCode($length) {
    $chars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $code;
}

$captcha = generateCaptchaCode($chars);
$_SESSION['captcha'] = strtolower($captcha);

$image = imagecreatetruecolor($width, $height);
$bg_color = imagecolorallocate($image, 245, 245, 245);
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

for ($i = 0; $i < $noise_lines; $i++) {
    $line_color = imagecolorallocate($image, 
        mt_rand(100, 200), 
        mt_rand(100, 200), 
        mt_rand(100, 200)
    );
    imageline($image, 
        mt_rand(0, $width), mt_rand(0, $height),
        mt_rand(0, $width), mt_rand(0, $height),
        $line_color
    );
}

for ($i = 0; $i < $noise_points; $i++) {
    $point_color = imagecolorallocate($image, 
        mt_rand(50, 150), 
        mt_rand(50, 150), 
        mt_rand(50, 150)
    );
    imagesetpixel($image, 
        mt_rand(0, $width), 
        mt_rand(0, $height), 
        $point_color
    );
}

$length = strlen($captcha);
for ($i = 0; $i < $length; $i++) {
    $text_color = imagecolorallocate($image, 
        mt_rand(0, 100), 
        mt_rand(0, 100), 
        mt_rand(0, 100)
    );

    $x = ($width / $chars) * $i + 10;
    $y = $height * 0.7;

    $angle = mt_rand(-15, 15);
    imagettftext(
        $image,
        $font_size,
        $angle,
        $x,
        $y,
        $text_color,
        $font_path,
        $captcha[$i]
    );
}

$temp_image = imagecreatetruecolor($width, $height);
imagecopy($temp_image, $image, 0, 0, 0, 0, $width, $height);
for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $new_x = $x + sin($y / 10) * 1;
        $new_y = $y + cos($x / 10) * 1;
        
        if ($new_x >= 0 && $new_x < $width && $new_y >= 0 && $new_y < $height) {
            $color = imagecolorat($temp_image, $x, $y);
            imagesetpixel($image, $new_x, $new_y, $color);
        }
    }
}
imagedestroy($temp_image);
header('Content-Type: image/png');
header('Cache-Control: no-cache, must-revalidate');
imagepng($image);
imagedestroy($image);
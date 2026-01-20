<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CaptchaController extends AbstractController
{
    #[Route('/captcha', name: 'app_captcha')]
    public function index(SessionInterface $session): Response
    {
        $code = rand(1000, 9999);
        $session->set('captcha_code', $code);

        $image = imagecreatetruecolor(100, 40);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);

        imagefill($image, 0, 0, $bgColor);
        imagestring($image, 5, 30, 12, (string)$code, $textColor);

        // Add some noise
        for ($i = 0; $i < 5; $i++) {
            imageline($image, rand(0, 100), rand(0, 40), rand(0, 100), rand(0, 40), $textColor);
        }

        ob_start();
        imagepng($image);
        $content = ob_get_clean();
        imagedestroy($image);

        return new Response($content, 200, ['Content-Type' => 'image/png']);
    }
}

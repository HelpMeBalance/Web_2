<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use App\Entity\User;
use App\Entity\PasswordResetToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Form\ResetPasswordFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class PasswordResetController extends AbstractController
{
    #[Route('/reset-password', name: 'app_reset_password')]
    public function request(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response
    {
        $email = $request->request->get('email');

        // If the form was submitted, find the user by email
        if ($email) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            // If the user exists, create a password reset token
            if ($user) {
                $resetToken = (new PasswordResetToken())
                    ->setToken($tokenGenerator->generateToken())
                    ->setExpiresAt(new \DateTime('+1 hour'))
                    ->setUser($user);

                $entityManager->persist($resetToken);
                $entityManager->flush();
                $mail = new PHPMailer(true);

                // Send an email with the reset token
                try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp-relay.brevo.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'fares2business@gmail.com';
                $mail->Password   = 's8qHyjANkQ9DUO7W'; // replace with your password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('fares2business@gmail.com', 'Mailer');
                $mail->addAddress('faresbrayek2@gmail.com', 'Joe User'); // Add a recipient

                // Content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Here is the subject';
                $mail->Body    = '<p>Here is your password reset link: <a href="http://localhost:8000/reset-password/' . $resetToken->getToken() . '">Reset Password</a></p>';
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                $mail->send();
                $message = 'Message has been sent';
            } catch (Exception $e) {
                $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }



            }

            // Always display the same message to prevent user enumeration
        $this->addFlash('success', 'Email sent successfully! Check your inbox.');
        }

        return $this->render('password_reset/request.html.twig', [
            'service' => 0,
            'part' => 199,
            'title' => 'Password RESET',
            'titlepage' => 'Password reset - ',
            'email' => $email,

        ]);
    }


    #[Route('/reset-password/{token}', name: 'app_reset_password_confirm')]
    public function reset(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, string $token): Response
    {
        $passwordResetToken = $entityManager->getRepository(PasswordResetToken::class)->findOneBy(['token' => $token]);

        if (!$passwordResetToken || $passwordResetToken->isExpired()) {
            return $this->redirectToRoute('app_reset_password');
        }

        $user = $passwordResetToken->getUser();
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $form['plainPassword']->getData())
            );

            $entityManager->remove($passwordResetToken);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('password_reset/reset.html.twig', [
            'service' => 0,
            'part' => 1,
            'title' => 'SET A Password',
            'titlepage' => 'Set a new password - ',
            'form' => $form->createView(),
        ]);
    }

}
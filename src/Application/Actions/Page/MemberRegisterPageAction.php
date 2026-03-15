<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use App\Domain\Member\MemberAuthRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Throwable;

class MemberRegisterPageAction extends AbstractPageAction
{
    private MemberAuthRepository $memberAuthRepository;

    public function __construct(LoggerInterface $logger, Twig $twig, MemberAuthRepository $memberAuthRepository)
    {
        parent::__construct($logger, $twig);
        $this->memberAuthRepository = $memberAuthRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $errors = [];
        $success = false;
        $form = [
            'full_name' => '',
            'email' => '',
        ];

        if (strtoupper($request->getMethod()) === 'POST') {
            $body = (array) ($request->getParsedBody() ?? []);
            $fullName = trim((string) ($body['full_name'] ?? ''));
            $email = strtolower(trim((string) ($body['email'] ?? '')));
            $password = (string) ($body['password'] ?? '');
            $passwordConfirmation = (string) ($body['password_confirmation'] ?? '');

            $form['full_name'] = $fullName;
            $form['email'] = $email;

            if ($fullName === '') {
                $errors[] = 'Informe seu nome completo.';
            }

            if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $errors[] = 'Informe um e-mail válido.';
            }

            if (strlen($password) < 8) {
                $errors[] = 'A senha deve ter ao menos 8 caracteres.';
            }

            if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
                $errors[] = 'A senha deve conter ao menos 1 letra maiúscula e 1 número.';
            }

            if ($password !== $passwordConfirmation) {
                $errors[] = 'A confirmação de senha não confere.';
            }

            if (empty($errors)) {
                try {
                    $this->memberAuthRepository->createPendingUser([
                        'full_name' => $fullName,
                        'email' => $email,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    ]);

                    $success = true;
                    $form = [
                        'full_name' => '',
                        'email' => '',
                    ];
                } catch (Throwable $exception) {
                    $errorMessage = strtolower($exception->getMessage());
                    $isDuplicate = str_contains($errorMessage, 'duplicate')
                        || str_contains($errorMessage, '1062')
                        || str_contains($errorMessage, 'unique')
                        || str_contains($errorMessage, 'email');

                    $this->logger->error('Falha ao criar cadastro pendente de membro.', [
                        'email' => $email,
                        'exception' => $exception,
                    ]);

                    $errors[] = $isDuplicate
                        ? 'Este e-mail já possui cadastro.'
                        : 'Cadastro indisponível no momento. Tente novamente em instantes.';
                }
            }
        }

        return $this->renderPage($response, 'pages/member-register.twig', [
            'member_register_errors' => $errors,
            'member_register_success' => $success,
            'member_register_form' => $form,
            'page_title' => 'Cadastro de Membro | CEDE',
            'page_url' => 'https://cedern.org/cadastro',
            'page_description' => 'Cadastro de frequentador para área de membros do CEDE.',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use App\Domain\Member\MemberAuthRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Throwable;

class AboutManagementPageAction extends AbstractPageAction
{
    private const ROLE_RESPONSIBILITIES = [
        'Presidente NatalCode' =>
            'Representa institucionalmente o NatalCode, coordena decisões estratégicas '
            . 'e acompanha o cumprimento do plano anual da operacao.',
        'Vice-presidente NatalCode' =>
            'Apoia a presidência na coordenação geral, acompanha frentes prioritárias '
            . 'e substitui a presidência quando necessário.',
        'Secretário' =>
            'Organiza registros administrativos, atas e comunicações internas '
            . 'para dar suporte à governança institucional.',
        'Diretor de Finanças' =>
            'Planeja e acompanha orçamento, receitas e despesas, promovendo '
            . 'uso responsável dos recursos da instituição.',
        'Diretor de Eventos' =>
            'Coordena planejamento e execução de eventos e atividades, '
            . 'alinhando logística, equipes e calendário institucional.',
        'Diretor de Patrimônio' =>
            'Zela pelos espaços e bens do NatalCode, organizando manutenção, '
            . 'conservação e uso adequado da infraestrutura.',
        'Diretor de Estudos' =>
            'Orienta frentes formativas e estudos de produto, estruturando '
            . 'conteúdos e acompanhando ciclos de aprendizagem.',
        'Diretor de Atendimento Fraterno' =>
            'Coordena suporte, atendimento e encaminhamento das demandas '
            . 'operacionais e comerciais.',
        'Diretor de Comunicação' =>
            'Conduz a comunicação institucional e os canais oficiais, garantindo '
            . 'clareza, unidade e responsabilidade na divulgação.',
        'Diretor de Governança e Compliance' =>
            'Estrutura políticas e controles de governança, compliance e integridade, '
            . 'monitorando conformidade com normas internas e externas.',
        'Diretor Jurídico' =>
            'Orienta juridicamente a instituição, revisa instrumentos formais e apoia '
            . 'a gestão de riscos legais e regulatórios.',
        'Diretor de Operações' =>
            'Coordena processos operacionais, define padrões de execução e acompanha '
            . 'indicadores para garantir eficiência e continuidade das atividades.',
        'Ouvidor' =>
            'Recebe manifestações institucionais, conduz tratativas e promove melhoria '
            . 'contínua na experiência de membros, parceiros e público.',
        'Conselheiro Fiscal' =>
            'Acompanha demonstrações e práticas financeiras, reforçando transparência, '
            . 'controle e responsabilidade na gestão de recursos.',
        'Conselheiro Consultivo' =>
            'Apoia decisões estratégicas com visão técnica e institucional, '
            . 'fortalecendo planejamento e sustentabilidade do NatalCode.',
        'Coordenador' =>
            'Acompanha a operação de uma frente específica, organiza equipe '
            . 'e garante execução das atividades previstas.',
        'Conselheiro' =>
            'Contribui com orientação e acompanhamento institucional, '
            . 'apoiando decisões e o fortalecimento da missão do NatalCode.',
    ];

    private MemberAuthRepository $memberAuthRepository;

    public function __construct(LoggerInterface $logger, Twig $twig, MemberAuthRepository $memberAuthRepository)
    {
        parent::__construct($logger, $twig);
        $this->memberAuthRepository = $memberAuthRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $managementMembers = [];

        try {
            $users = $this->memberAuthRepository->findAllUsersForAdmin();

            $managementMembers = array_values(array_filter(
                $users,
                static fn (array $user): bool =>
                    (string) ($user['status'] ?? '') === 'active'
                    && trim((string) ($user['institutional_role'] ?? '')) !== ''
            ));

            usort($managementMembers, static function (array $first, array $second): int {
                return strnatcasecmp(
                    (string) ($first['institutional_role'] ?? '') . ' ' . (string) ($first['full_name'] ?? ''),
                    (string) ($second['institutional_role'] ?? '') . ' ' . (string) ($second['full_name'] ?? '')
                );
            });

            $managementMembers = array_map(function (array $member): array {
                $role = trim((string) ($member['institutional_role'] ?? ''));
                $member['institutional_role_description'] = self::ROLE_RESPONSIBILITIES[$role]
                    ?? 'Atua na organização e no fortalecimento das atividades institucionais do NatalCode.';

                return $member;
            }, $managementMembers);
        } catch (Throwable $exception) {
            $this->logger->warning('Falha ao carregar gestão NatalCode para página dedicada pública.', [
                'exception' => $exception,
            ]);
        }

        return $this->renderPage($response, 'pages/about-management.twig', [
            'public_cede_management' => $managementMembers,
            'page_title' => 'Gestão NatalCode | Quem Somos | NatalCode',
            'page_url' => 'https://natalcode.com.br/quem-somos/equipe',
            'page_description' =>
                'Conheça a composição da gestão atual do NatalCode '
                . 'e as atribuições institucionais de cada função.',
        ]);
    }
}

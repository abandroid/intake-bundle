<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Bundle\IntakeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Endroid\Bundle\IntakeBundle\Entity\Answer;
use Endroid\Bundle\IntakeBundle\Entity\Intake;
use Endroid\Bundle\IntakeBundle\Entity\Level;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Security("has_role('ROLE_INTAKE')")
 */
class IntakeController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $intakes = $this->getIntakeRepository()->findAll();

        return [
            'intakes' => $intakes,
        ];
    }

    /**
     * @Route("/{intake}")
     * @Template()
     *
     * @param Intake        $intake
     * @param UserInterface $user
     *
     * @return array
     */
    public function showAction(Intake $intake, UserInterface $user)
    {
        $level = null;
        $lastLevel = null;
        foreach ($intake->getLevels() as $level) {
            if (!$level->getCompleted($user)) {
                break;
            }
        }

        return [
            'intake' => $intake,
            'level' => $level,
        ];
    }

    /**
     * @Route("/{intake}/level/{level}/set-answers")
     * @Template()
     *
     * @param Request       $request
     * @param UserInterface $user
     * @param Level         $level
     * @param Intake        $intake
     *
     * @return Response
     */
    public function setAnswersAction(Request $request, UserInterface $user, Level $level, Intake $intake)
    {
        $answers = (array) $request->request->get('answers');

        foreach ($answers as $questionId => $answerContent) {
            $question = $this->getPartRepository()->findOneBy(['id' => $questionId]);
            $answer = $question->getAnswer($user);
            if (!$answer) {
                $answer = new Answer();
                $answer->setUser($user);
                $question->addAnswer($answer);
            }
            $answer->setContent($answerContent);
        }

        $this->getEntityManager()->flush();
        $this->addFlash('notice', 'Wijzigingen opgeslagen');

        // Mail after flush to avoid loss of answers due to mail issues
        if ($level->getTextsCompleted($user) && $level->getExtrasCompleted($user)) {
            $this->sendSummary($user, $intake);
        }

        return $this->redirect($this->generateUrl('endroid_intake_intake_show', ['intake' => $intake->getId()]));
    }

    /**
     * Sends the summary.
     *
     * @param Intake        $intake
     * @param UserInterface $user
     */
    protected function sendSummary(UserInterface $user, Intake $intake)
    {
        $body = '<html><body><table>';
        foreach ($intake->getLevels() as $level) {
            $body .= '<tr><td><strong>'.$level->getName().': '.$level->getAssessment($user).'</strong></td></tr>';
            foreach ($level->getTexts() as $text) {
                $body .= '<tr><td><em>'.$text->getTitle().'</em></td></tr>';
                $body .= '<tr><td>'.$text->getContent($user).'</td></tr>';
                $body .= '<tr><td>&nbsp;</td></tr>';
            }
            if ($level->getExtrasCompleted($user)) {
                foreach ($level->getExtras() as $index => $extra) {
                    $body .= '<tr><td><em>Extra aanvulzin '.($index + 1).'</em></td></tr>';
                    $body .= '<tr><td>'.$extra->getContent($user).'</td></tr>';
                }
                $body .= '<tr><td>&nbsp;</td></tr>';
            }
        }
        $body .= '</table></body>';

        $message = Swift_Message::newInstance();
        $message
            ->setSubject('Intake 2016 - '.$user->getUsername())
            ->setFrom('info@endroid.nl')
            ->setTo($intake->getEmailAddress())
            ->setBody($body, 'text/html')
        ;

        $this->get('mailer')->send($message);
    }

    /**
     * Returns the answer repository.
     *
     * @return EntityRepository
     */
    protected function getAnswerRepository()
    {
        return $this->getDoctrine()->getRepository('EndroidIntakeBundle:Answer');
    }

    /**
     * Returns the intake repository.
     *
     * @return EntityRepository
     */
    protected function getIntakeRepository()
    {
        return $this->getDoctrine()->getRepository('EndroidIntakeBundle:Intake');
    }

    /**
     * Returns the part repository.
     *
     * @return EntityRepository
     */
    protected function getPartRepository()
    {
        return $this->getDoctrine()->getRepository('EndroidIntakeBundle:Part');
    }

    /**
     * Returns the entity manager.
     *
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }
}

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
use UserBundle\Entity\User;

/**
 * @Security("has_role('ROLE_INTAKE')")
 */
class IntakeController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $intakes = $this->getIntakeRepository()->findAll();

        return array(
            'intakes' => $intakes,
        );
    }

    /**
     * @Route("/{intake}")
     * @Template()
     */
    public function showAction(Intake $intake)
    {
        $user = $this->getUser();

        $level = null;
        $lastLevel = null;
        foreach ($intake->getLevels() as $level) {
            if (!$level->getTextsCompleted($user) || !$level->getExtrasCompleted($user)) {
                break;
            }
            if ($level->getTextsCompleted($user) && $level->getExtrasCompleted($user)) {
                break;
            }
        }

        return array(
            'intake' => $intake,
            'level' => $level,
        );
    }

    /**
     * @Route("/{intake}/level/{level}/set-answers")
     * @Template()
     */
    public function setAnswersAction(Request $request, Level $level, Intake $intake)
    {
        $user = $this->getUser();
        $answers = (array) $request->request->get('answers');

        foreach ($answers as $questionId => $answerContent) {
            $question = $this->getPartRepository()->findOneBy(array('id' => $questionId));
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
            $this->sendSummary($intake);
        }

        return $this->redirect($this->generateUrl('endroid_intake_intake_show', array('intake' => $intake->getId())));
    }

    /**
     * Sends the summary.
     *
     * @param Intake $intake
     */
    protected function sendSummary(Intake $intake)
    {
        /** @var User $user */
        $user = $this->getUser();

        $body = '<html><body><table>';
        foreach ($intake->getLevels() as $level) {
            $body .= '<tr><td><strong>'.$level->getName().': '.$level->getAssessment($user).'</strong></td></tr>';
            foreach ($level->getTexts() as $text) {
                $body .= '<tr><td><em>'.$text->getTitle().'</em></td></tr>';
                $body .= '<tr><td>'.$text->getContent($user).'</td></tr>';
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

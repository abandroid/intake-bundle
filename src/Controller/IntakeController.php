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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
    public function showAction(Request $request, Intake $intake)
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
            $this->getEntityManager()->persist($answer);
        }

        if (count($answers) > 0) {
            $this->getEntityManager()->flush();
            $this->addFlash('notice', 'Wijzigingen opgeslagen');
        }

        $extras = null;
        $lastLevel = null;
        foreach ($intake->getLevels() as $level) {
            //            if ($level->getCompleted()) {
//                $lastLevel = $level;
//                continue;
//            }
        }

        return array(
            'intake' => $intake,
            'level' => $level,
            'extras' => $extras,
        );
    }

    /**
     * @Route("/{level}")
     * @Template()
     */
    public function levelAction(Level $level)
    {
        return array(
            'level' => $level,
        );
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

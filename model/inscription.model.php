<?php
	namespace gnk\model;
	use \gnk\config\Database;
	use \gnk\config\Tools;
	use \gnk\database\entities\Users;
	use \gnk\database\entities\VerifyUsers;
	
	/**
	* Modèle d'inscription
	* @todo Supprimer le verifUsers correspondant à l'utilisateur lorsqu'il clique sur le lien puis mettre à jour le champ "active" de Users
	* @todo Traitement du formulaire de récupération de mot de passe
	*/
	class Inscription{
		private $em;
		private $username;
		private $password;
		private $mail;
		private $subject;
		private $message;
		private $id;
		private $indications = array();
		
		/**
		* Constructeur
		*/
		public function __construct(){
			Database::useTables();
			$this->em = Database::getEM();
		}
		
		/**
		* Indique si l'utilisateur existe déjà ou non
		* @return boolean
		*/
		private function isUser(){
			$qb = $this->em->createQueryBuilder();
			$qb->select($qb->expr()->count('u.id'))
				->from('\gnk\database\entities\Users', 'u')
				->where($qb->expr()->orX(
					$qb->expr()->like('u.login', '?1'),
					$qb->expr()->like('u.mail', '?2')
				));
			$qb->setParameters(array(1 => $this->username, 2 => $this->mail));
			$query = $qb->getQuery();
			$result = $query->getSingleResult();
			if($result[1] > 0){
				return true;
			}
			return false;
		}
		
		public function activeUser($id, $key){
			$qb = $this->em->createQueryBuilder();
			$qb->select(array('v'))
				->from('\gnk\database\entities\VerifyUsers', 'v')
				->where('v.user = :id')
				->andWhere('v.userkey = :userkey');
			$qb->setParameters(array('id' => $id, 'userkey' => sha1($key)));
			$query = $qb->getQuery();
			$result = $query->getResult();
			if(count($result)>0){
				$result[0]->getUser()->setActive(true);
				$this->indications[]=T_('Vous pouvez maintenant vous connecter');
				$this->em->persist($result[0]->getUser());
				$this->em->remove($result[0]);
				$this->em->flush();
			}
		}
		public function deleteUser($id, $key){
			$qb = $this->em->createQueryBuilder();
			$qb->select(array('v'))
				->from('\gnk\database\entities\VerifyUsers', 'v')
				->where('v.user = :id')
				->andWhere('v.userkey = :userkey');
			$qb->setParameters(array('id' => $id, 'userkey' => sha1($key)));
			$query = $qb->getQuery();
			$result = $query->getResult();
			if(count($result)>0){
				$this->em->remove($result[0]->getUser());
				$this->em->remove($result[0]);
				$this->em->flush();
				$this->indications[]=T_('Utilisateur supprimé de la base de donnée');
			}
		}
		
		/**
		* Ajoute un utilisateur en lui demandant une confirmation par mail
		* @param string $username
		* @param string $password
		* @param string $mail
		* @todo Indiquer l'erreur s'il y en a une
		*/
		public function addUser($username, $password, $mail){
			$this->username = $username;
			$this->password = $password;
			$this->mail=$mail;
			$this->key = sha1(uniqid(rand(), true));
			if(!$this->isUser()){
				$user=new Users($username, $password, $mail);
				$this->em->persist($user);
				$this->em->flush();
				return $this->verificationUser($user);
			}
			else{
// 				echo T_('Un utilisateur porte déjà ce login ou ce mail');
				return false;
			}
		}
		
		/**
		* Envoi un mail de vérification à l'utilisateur
		*/
		private function verificationUser($user){
			$verif=new VerifyUsers($user, $this->key);
			$this->em->persist($verif);
			$this->em->flush();
			$this->id = $user->getId();
			//Envoi du message
			$this->getSubject();
			$this->getMessage();
			if(Tools::sendmail($this->mail, $this->subject, $this->message)){
// 					echo T_('Utilisateur ajouté');
				return true;
			}
			else{
				$this->em->remove($verif);
				$this->flush();
// 					echo T_('Envoi du mail échoué, veuillez récupérer votre mot de passe via le formulaire de mots de passes oubliés');
				return false;
			}
		}
		
		/**
		* Permet de récupérer le sujet du message
		* @todo Rendre plus générique en mettant le "réel" nom du site dans le sujet
		*/
		private function getSubject(){
			$this->subject = T_('Inscription à LocalizeTeaPot');
		}
		
		/**
		* Permet de définir le message à envoyer à la personne qui s'inscrit
		* @todo Rendre plus générique en mettant le "réel" nom du site dans le message
		*/
		private function getMessage(){
			$this->message = 'Bienvenue sur LocalizeTeaPot,'."\n".'Vous pouvez terminer votre inscription en cliquant sur'."\n";
			$this->message .= 'http';
			if(isset($_SERVER['HTTPS'])){
				$this->message .= 's';
			}
			$this->message .=  '://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			if(isset($_GET)){
				$this->message .= '&';
			}
			else{
				$this->message .= '?';
			}
			$this->message .= 'id='.$this->id.'&key='.$this->key;
		}
		
		public function getInfo(){
			return $this->indications;
		}
	}
?>
<?php
/*
*
* Copyright (c) 2012 OpenTeamMap
*
* This file is part of LocalizeTeaPot.
*
* LocalizeTeaPot is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* LocalizeTeaPot is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with LocalizeTeaPot.  If not, see <http://www.gnu.org/licenses/>.
*/

	use \gnk\config\Page;
	use \gnk\config\Template;
	use \gnk\config\Controller;
	Controller::load('statusmanager');
	
	if(Page::haveRights(3)){
		$controller = new \gnk\controller\StatusManager();
		$osm = $controller->getMap('status_map');
		if(isset($_GET['add'])){
			$form = $controller->getAddForm();
		}
		else if(isset($_GET['edit'])){
			$form = $controller->getEditForm();
		}
		$template = new Template();
		$template->addTitle(T_('Statuts'));
		$template->setDescription(T_('Gestion des statuts.'));
		$template->addKeywords(array(T_('statuts')));
		$template->show('header_full');
?>
	<article>
		<h1><?php echo T_("Mes statuts");?></h1>
<?php

			if(isset($_GET['delete']) AND !isset($_GET['confirm'])){
?>
		<div class="delete">
			<p>Voulez-vous supprimer ce statut ?</p>
			<ul>
				<li><a href="<?php echo Page::getLink().'?delete='.$_GET['delete'] . '&amp;confirm'; ?>">Oui</a></li>
				<li><a href="<?php echo Page::getLink() ;?>">Non</a></li>
			</ul>
		</div>
<?php
			}
?>
		<div id="status">
<?php
			$osm->showDiv();
?>
			<div id="statuses">
<?php

			if(isset($_GET['add']) OR isset($_GET['edit'])){
?>
			<ul class="action">
				<li><a href="<?php echo Page::getLink(); ?>"><?php echo Page::getImage('back', T_('Revenir aux statuts'), 16) . ' ' . T_('Revenir aux statuts');?></a></li>
			</ul>
<?php
				$form->render();
			}
			else{
?>
			<ul class="action">
				<li><a href="<?php echo Page::getLink(array('add' => '')) ; ?>"><?php echo Page::getImage('add', T_('Ajouter un statut'), 16) . ' ' . T_('Ajouter un statut');?></a></li>
			</ul>
<?php
			}
			if(!isset($_GET['edit'])){
				foreach($controller->getStatuses() as $nStatus => $stat){
?>
				<section>
					<h1><?php echo Page::htmlEncode(date_format($stat['date'], "d/m/Y H:i:s"));?></h1>
					<p>
						<?php echo Page::htmlBREncode($stat['message']);?>
					</p>
					<ul>
						<li>
							
							<a href="<?php echo Page::getLink(array('edit' => $stat['id']));?>">
								<?php echo Page::getImage('edit', T_('Éditer'), 16);?>
							</a>
						</li>
						<li>
							<a href="<?php echo Page::getLink(array('delete' => $stat['id']));?>">
								<?php echo Page::getImage('delete', T_('Supprimer'), 16);?>
							</a>
						</li>
					</li>
				</section>
<?php
				}
			}
		?>
			</div>
		</div>
	</article>
<?php
		$template->show('footer_full');
	}
	else{
		Page::showDefault();
	}
?>
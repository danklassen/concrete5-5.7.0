<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;
use \Concrete\Core\Conversation\FlagType\FlagType as ConversationFlagType;

$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');
$as = Loader::helper('validation/antispam');

$pageObj = Page::getByID($_POST['cID']);
$areaObj = Area::get($pageObj, $_POST['blockAreaHandle']);
$blockObj = Block::getByID($_POST['bID'], $pageObj, $areaObj);

$form = Loader::helper('form');

$u = new User();
$ui = UserInfo::getByID($u->getUserID());
$val = Loader::helper('validation/token');

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
	$message = ConversationMessage::getByID($_POST['cnvMessageID']);
	if (is_object($message)) {
        $mp = new Permissions($message);
        if ($mp->canEditConversationMessage()) {
            $editor = \Concrete\Core\Conversation\Editor\Editor::getByID($message->getConversationEditorID());
            $editor->setConversationMessageObject($message);
            ?>

            <div class="ccm-conversation-edit-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>">
                <form method="post" class="aux-reply-form">
                    <div class="ccm-conversation-avatar"><? print Loader::helper('concrete/avatar')->outputUserAvatar($ui)?></div>
                    <div class="ccm-conversation-message-form">
                        <div class="ccm-conversation-errors alert alert-danger"></div>
                        <? $editor->outputConversationEditorReplyMessageForm(); ?>
                        <button type="button" data-post-message-id="<?=$message->getConversationMessageID()?>" data-submit="update-conversation-message" class="pull-right btn btn-primary btn-small"><?=t('Save')?></button>
                        <button type="button" class="pull-right btn btn-default ccm-conversation-attachment-toggle" title="<?php echo t('Attach Files'); ?>"><i class="fa fa-image"></i></button>
                        <button type="button" data-post-message-id="<?=$message->getConversationMessageID()?>" data-submit="cancel-update" class="cancel-update pull-right btn btn-small"><?=t('Cancel')?></button>
                        <?php echo $form->hidden('blockAreaHandle', $blockAreaHandle) ?>
                        <?php echo $form->hidden('cID', $cID) ?>
                        <?php echo $form->hidden('bID', $bID) ?>
                    </div>
                </form>
                <div class="ccm-conversation-attachment-container">
                    <form action="<?php echo Loader::helper('concrete/urls')->getToolsURL('conversations/add_file');?>" class="dropzone" id="file-upload-reply">
                        <div class="ccm-conversation-errors alert alert-danger"></div>
                        <?php $val->output('add_conversations_file'); ?>
                        <?php echo $form->hidden('blockAreaHandle', $blockAreaHandle) ?>
                        <?php echo $form->hidden('cID', $cID) ?>
                        <?php echo $form->hidden('bID', $bID) ?>
                    </form>
                </div>
            </div>


        <?

        }
	}
}

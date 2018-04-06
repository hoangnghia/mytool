<? use luya\admin\ngrest\aw\CallbackFormWidget; ?>
<h4>Group Contacts</h4>
<table class="striped">
    <thead>
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
    </tr>
    </thead>
    <? foreach ($contacts as $contact): ?>
        <tr>
            <td><?= $contact->firstname; ?></td>
            <td><?= $contact->lastname; ?></td>
            <td><?= $contact->email; ?></td>
        </tr>
    <? endforeach; ?>
</table>

<h4>Send email</h4>
<p>Write an email to all contacts in this group.</p>

<? $form = CallbackFormWidget::begin([
    'callback' => 'send-mail',
    'buttonValue' => 'Send',
    'options' => ['closeOnSuccess' => true]
]); ?>
<?= $form->field('subject', 'Subject'); ?>
<?= $form->field('text', 'Text')->textarea(); ?>
<? $form::end(); ?>

<div>
    <a href="<?= $route = \luya\helpers\Url::toRoute(['/addressbook']); ?>">< Back</a>
</div>
<br/>

<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'salutation',
        'firstname',
        'lastname',
        'street',
        'zip',
        'city',
        'country',
        'email',
        'notes:html',
    ],
]);
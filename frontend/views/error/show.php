<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = "OMG..出错了..";
?>
<style>
	.error-trace {
		margin:20px auto;
		padding:0 10px;
	}

</style>
<div class="site-error">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="alert alert-danger">
		<?= nl2br(Html::encode($exception->getMessage())) ?>
	</div>
	<?php if(YII_ENV!=='prod'): ?>
		<div class="error-trace">
				<?php echo sprintf("%s 第 %s 行错误 。 <br/>%s",
						$exception->getFile(),
						$exception->getLine(),
						nl2br($exception->getTraceAsString())
					);  ?>
		</div>
	<?php endif; ?>

</div>
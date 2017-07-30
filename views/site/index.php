<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <div class="col-lg-6">
                <h2>Building</h2>


                <?php for ($i = 5 ; $i > 0 ; $i--):?>
                    <row>
                        <h3>Floor #<?=$i?></h3>
                        <div class="col-lg-6">
                            <a href="/" class="text-danger"><span class="glyphicon glyphicon-circle-arrow-down" title="Down"></span></a>
                            <a href="/" class="text-success"><span class="glyphicon glyphicon-circle-arrow-up" title="Up"></span></a>
                        </div>
                        <div class="col-lg-6">
                            Peoples waiting: 0
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                    </row>
                <?php endfor;?>

            </div>
            <div class="col-lg-6">
                <h2>Elevator</h2>
            </div>
        </div>

    </div>
</div>

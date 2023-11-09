<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class AlertAction extends Action
{
    public function execute(): string
    {
        $contenuHtml = <<<HTML
            <div class="modal-container">
                <input id="modal-toggle" type="checkbox">
                <label class="modal-btn" for="modal-toggle">
                    <img src="image/equitation.png"  width=100% height=100%>
                </label> 
                <label class="modal-backdrop" for="modal-toggle"></label>
                <div class="modal-content">
                    <label class="modal-close" for="modal-toggle">&#x2715;</label>
                    <img src="image/equitation.png"> 
                </div>          
            </div>
        HTML;
        return $contenuHtml;
    }
}

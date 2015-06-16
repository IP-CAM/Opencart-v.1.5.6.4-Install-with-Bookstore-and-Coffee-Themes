<div id="footer">
  <?php if ($informations) { ?>
  <div class="column">
    <h3><?php echo $text_information; ?></h3>
    <ul class="columntext">
      <?php foreach ($informations as $information) { ?>
      <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
      <?php } ?>
      <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
    </ul>
  </div>
  <?php } ?>
  <div class="column">
    <h3><?php echo $text_extra; ?></h3>
    <ul class="columntext">
      <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
      <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
      <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
      <li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
      <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
    </ul>
  </div>
  <div class="column">
    <h3><?php //echo $text_payfooter; ?></h3>
    <ul  class="columnimg">
        <li>
            <img src="catalog/view/theme/coffee/image/yandexmoney.gif">
            <img src="catalog/view/theme/coffee/image/webmoney.gif">
        </li>
        <li>
            <img src="catalog/view/theme/coffee/image/mastercard.gif">
            <img src="catalog/view/theme/coffee/image/visa.gif">
        </li>
        <li>
            <img src="catalog/view/theme/coffee/image/qiwi.gif">
            <img src="catalog/view/theme/coffee/image/paypal.gif">
        </li>
    </ul>
  </div>
   <div class="column">
    <h3><?php// echo $text_socialfooter; ?></h3>
    <ul  class="columnimg">
        <li>
            <a href="#"><img src="catalog/view/theme/coffee/image/Facebook.png"></a>
            <a href="#"><img src="catalog/view/theme/coffee/image/VKontakte.png"></a>
            <a href="#"><img src="catalog/view/theme/coffee/image/Odnoklassniki.png"></a>
        </li>
        <li>
            <a href="#"><img src="catalog/view/theme/coffee/image/Google+.png"></a>
            <a href="#"><img src="catalog/view/theme/coffee/image/Twitter 1.png"></a>
            <a href="#"><img src="catalog/view/theme/coffee/image/You Tube.png"></a>
        </li>
    </ul>
  </div>
</div>
</body></html>
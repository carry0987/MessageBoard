<?php
$today = date("l F jS, Y");
?>
    <footer>
      <div id="footericon">
          <a href="mailto:carry0987@gmail.com" title="carry0987 Email" style="text-decoration:none;" target="_blank">
          <img class="imgspace" src="../static/icon.php?file=email.svg"  width="30px"/>
          </a>
          <a href="https://www.facebook.com/carry0987/" title="carry0987 Facebook" style="text-decoration:none;" target="_blank">
          <img class="imgspace" src="../static/icon.php?file=facebook.svg"  width="30px"/>
          </a>
          <a href="https://github.com/carry0987/" title="carry0987 GitHub" style="text-decoration:none;" target="_blank">
          <img class="imgspace" src="../static/icon.php?file=github.svg"  width="30px"/>
          </a>
      </div>
      <?php echo "\t".'<p>&copy 2017 - '.$today.'</p>'."\n"; ?>
    <div id="footer">
        <p>Made By <a class="developer" href="https://carry0987.github.io/" target="_blank">carry0987</a> <?php echo $version; ?></p>
    </div>
  </footer>
  </div>
</body>
</html>
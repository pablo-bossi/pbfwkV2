<html>
  <?php echo $this->renderSubModule('helloworld/htmlhead'); ?>
  <body style="width:960px; margin:0 auto;">
    <header>
      <div style="width:100%; background-color:grey;min-height:50px;line-height:50px;vertical-align:middle;padding-left:20px;">
        <strong>Here it goes the header</strong>
      </div>
    </header>
    <article id="content">
      <?php echo $this->renderSubModule($this->childModule); ?>
    </article>
    <footer>
      <div style="width:100%; background-color:grey;min-height:50px;line-height:50px;vertical-align:middle;padding-left:20px;">
        Here it goes the footer
      </div>
    </footer>
  </body>
</html>
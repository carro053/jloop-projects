<h1>Soap error</h1>
<ul>
    <li><?php echo $html->link('Back to home', '/'); ?></li>
</ul>    
<div>
    <h2>Error</h2>
    <?php pr($error);?>
</div>    
<div>
    <h2>Debug</h2>
    <?php pr($dbgStr);?>
</div>    
<div>
    <h2>Request</h2>
    <?php pr($dbgRequest);?>
</div>    
<div>
    <h2>Response</h2>
    <?php pr($dbgResponse);?>
</div>
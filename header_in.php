<?php
echo "<header>
                <span><a href=\"index.php\">COS Filer </a></span>
                <nav>
                    <ul>
                        <li><a href=\"index.php\">Accueil</a></li>
                        <li><a href=\"my_files.php\">Mes fichiers</a></li>
                        <li><a href=\"log_out.php\"><img src='img/logout.png' alt='exit'/></a></li>
                        <li id='user_session'>&nbsp;&nbsp;&nbsp;&nbsp;<img src='img/route.png' alt='route'/>&nbsp;&nbsp;".$_SESSION['username'] . "</li>
                    </ul>
                </nav>
            </header>";

?>
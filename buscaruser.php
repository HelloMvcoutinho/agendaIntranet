<!DOCTYPE html>
<html>
	<head>
		<title>Agenda</title>
	</head>
	<body>
			<?php
				$ldap_password = 'password';
				$ldap_username = 'username';
				$ldapconfig['host']='###.###.###.###';
				$ldapconfig['port']= ###;
				$domain = '####.####';
				

				$ds = ldap_connect ($ldapconfig['host'], $ldapconfig['port']) 
					or die ("Não pôde se conectar ao ".$ldapconfig['host'].":".$ldapconfig['port'].".");

				ldap_set_option ($ds, LDAP_OPT_PROTOCOL_VERSION, 3) 
					or die ('Não foi possível definir a versão do protocolo LDAP');
				ldap_set_option ($ds, LDAP_OPT_REFERRALS, 0); // Precisamos disso para fazer uma pesquisa LDAP.

				if($ds){
					if($ldapbind = ldap_bind ($ds, $ldap_username.'@'.$domain, $ldap_password)){
						$ldap_base_dn = 'DC = ####, DC = ####';
					    //$search_filter = '(& (objectCategory = person) (samaccountname = *))';
					    //$search_filter = "(&(objectClass=user)(objectCategory=person)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))";
					    $search_filter = "(&(objectClass=user)(objectCategory=person)(displayname=*))";
					    $attributes = array ();
					    $attributes [] = 'displayname';
					    $attributes [] = 'telephonenumber';
					    $attributes [] = 'ipphone';
					    $attributes [] = 'mobile';
					    $attributes [] = 'mail';
					    $attributes [] = 'useraccountcontrol';

				    	$result_search = ldap_search ($ds, $ldap_base_dn, $search_filter, $attributes);
				    	if($result_search){
					    	$info = ldap_get_entries($ds, $result_search);
					    	//faz ordenação do array
					    	usort($info, function($a, $b) {
    							return strcmp($a['displayname'][0], $b['displayname'][0]);
							});
							$retorno=ldap_count_entries($ds,$result_search);
										$tabela  = '<div id ="scrollbar">';
									   	$tabela .= '<table id="myTable">';//abre table 
									    $tabela .= '<thead>';//abre cabeçalho
									    $tabela .= '<tr>';//abre uma linha
									    $tabela .= '<th>Nome</th>'; // colunas do cabeçalho
									    $tabela .= '<th>Telefone</th>';
									    $tabela .= '<th>Ramal</th>';
									    $tabela .= '<th>Celular</th>';
									    $tabela .= '<th>Email</th>';
									    $tabela .= '</tr>';//fecha linha
									    $tabela .= '</thead>';
										$tabela .= '<tbody>';//abre corpo da tabela
						   for ($i=0; $i < $retorno; $i++){
							   if(isset($info[$i]["telephonenumber"][0])){
									if(isset($info[$i]["displayname"][0])){
										$name = $info[$i]["displayname"][0];
									}
						        	if(isset($info[$i]["mail"][0])){
										$mail = $info[$i]["mail"][0];
									}else{
										$mail = "";
									}
									if(isset($info[$i]["telephonenumber"][0]))	{
										$phone = $info[$i]["telephonenumber"][0];
									}
									if(isset($info[$i]["ipphone"][0]))
									{
										$ipphone = $info[$i]["ipphone"][0];
									}else{
										$ipphone = "Sem ramal";
									}
									if(isset($info[$i]["mobile"][0]))
									{
										$mobile = $info[$i]["mobile"][0];
									}else{
										$mobile = "";	
									}
									if(isset($info[$i]["useraccountcontrol"][0])){
										$state =  $info[$i]["useraccountcontrol"][0];
									}
									
							 		$state = @dechex($state);
							 		
									$state = @substr($state,-1,1);//verifica contas desabilitadas
									
								   if ( $name != "" && $phone != "" && $state != 2){
									    /*Se você tiver um loop para exibir os dados ele deve ficar aqui*/
									    $tabela .= '<tr>'; // abre uma linha
									    $tabela .= '<td>'.$name.'</td>'; // coluna nome
									    $tabela .= '<td>'.$phone.'</td>'; // coluna telefone
									    $tabela .= '<td>'.$ipphone.'</td>'; //coluna ramal
									    $tabela .= '<td>'.$mobile.'</td>'; //coluna ramal
									    $tabela .= '<td>'.$mail.'</td>'; //coluna email
									    $tabela .= '</tr>'; // fecha linha
									    /*loop deve terminar aqui*/	
								    
									}
								    
								}
									
							}
										$tabela .= '</tbody>'; //fecha corpo
									    $tabela .= '</table>';//fecha tabela
									    $tabela .= '</div>'; //fecha cabeçalho
									    echo $tabela; // imprime
									   
							}

				    	}   
				}else{
					echo "LDAP ligação falhou ...";
				}
			
				ldap_close($ds);
			?>
	</body>
</html>
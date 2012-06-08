		<article>
			<a id="close" class="touch" href="">×</a>
			<div>
				<h2>Register</h2>
				<?php 
					echo $this->Form->create('User');
					echo $this->Form->input('username');
					echo $this->Form->input('password1');
					echo $this->Form->input('password2');
					echo $this->Form->end('✓');
				?>
				<form>
					<input type="text" placeholder="Username" />
					<input type="password" placeholder="Password" />
					<input type="password" placeholder="Password" />
					<input type="submit" value="✓" />
				</form>
			</div>
		</article>
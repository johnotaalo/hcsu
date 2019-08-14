<template>
	<div>
		

		<!-- <div class="header">
			<div class="row align-items-center">
				<div class="col">
					<ul class="nav nav-tabs nav-overflow header-tabs">
						<li class="nav-item"><router-link class="nav-link" :to="{ name: 'blanket' }">Blanket VAT</router-link></li>
						<li class="nav-item"<router-link class="nav-link" :to="{ name: 'normal-vat' }">Normal VAT</router-link></li>
					</ul>
				</div>
			</div>
		</div> -->

		<div class="row">
			<div class="col-12 col-xl-12">
				<div class="card">
					<div class="card-body">
						<ul class="nav nav-tabs mb-4">
							<li class="nav-item"><router-link class="nav-link" :to="{ name: 'blanket' }">Blanket VAT</router-link></li>
							<li class="nav-item"<router-link class="nav-link" :to="{ name: 'normal-vat' }">Normal VAT</router-link></li>
						</ul>
						<router-view></router-view>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</template>
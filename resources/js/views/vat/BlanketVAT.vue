<template>
	<div>
		<div class="row">
			<div class="col-12 col-xl-12">
				<div class="card">
					<div class="card-header">
						<h3>BLANKET V.A.T</h3>
					</div>
					<div class="card-body">
						<ul class="nav nav-tabs mb-4">
							<li class="nav-item"><router-link class="nav-link" :to="{ name: 'blanket-vat-batches' }">Batches</router-link></li>
							<li class="nav-item"><router-link class="nav-link" :to="{ name: 'normal-vat' }">Normal VAT</router-link></li>
						</ul>
						<router-view></router-view>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</template>
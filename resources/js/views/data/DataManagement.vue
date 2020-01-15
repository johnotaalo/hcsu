<template>
	<div>
		<div class="header">
			<div class="header-body">
				<div class="row align-items-center">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle">
							Data Tool
						</h6>
						<!-- Title -->
						<h1 class="header-title">
							Data Management
						</h1>
					</div>
				</div> <!-- / .row -->
			</div>
		</div>

		<b-card no-body>
			<b-tabs small card>
				<b-tab title="V.A.T Suppliers" active>
					<vat-suppliers></vat-suppliers>
				</b-tab>
				<b-tab title="Blanket V.A.T Suppliers"></b-tab>
			</b-tabs>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	import VATSuppliers from './components/VATSuppliers'
	export default {
		name: 'DataManagement',
		components: { "vat-suppliers": VATSuppliers }
	}
</script>
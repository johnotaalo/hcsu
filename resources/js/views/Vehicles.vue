<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col">
						<!-- Title -->
						<h4 class="card-header-title">
						<i class="fe fe-truck mr-2"></i>Vehicles
						</h4>

					</div>
					<div class="col-auto">
						<b-button class= "float-right" :to="{ name: 'principal.add' }" variant="outline-primary" size ="sm"><i class="fe fe-plus-circle"></i>&nbsp;Add Vehicle</b-button>
					</div>
				</div>
			</div>
			<div class="card-body">
				<vehicle-list></vehicle-list>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import VehicleList from './vehicle/VehicleList'
	export default {
		components: {VehicleList},
		data() {
			return {
			}
		}
	}
</script>
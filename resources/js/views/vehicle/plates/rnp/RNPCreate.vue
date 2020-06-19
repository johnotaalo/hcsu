<template>
	<div>
		<div class="header">
			<div class="header-body">
				<div class="row align-items-center">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle">
							RETURNED PLATES
						</h6>
						<!-- Title -->
						<h1 class="header-title">
							CREATE RNP LIST
						</h1>
					</div>
				</div> <!-- / .row -->
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<div class="row align-items-center">
					<div class="col">
						<b-button class="btn btn-default" :to="{ name: 'rnp-list' }"><i class="fe fe-arrow-left"></i>&nbsp;&nbsp;Back to List</b-button>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md">
						<b-form-group label="RNP Date">
							<b-input type="date"/>
						</b-form-group>
					</div>
				</div>
				<div class="row">
					
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default{

	}
</script>
<template>
	<div class="header bg-dark pb-5">
		<div class="container-fluid">
			<div class="header-body">
				<div class="row align-items-end">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle text-secondary">
							Overview
						</h6>

						<!-- Title -->
						<h1 class="header-title text-white">
							Applications
						</h1>
					</div>
					<div class="col-auto">

					</div>
				</div> <!-- / .row -->
			</div>
			<div class="header-footer">
				<div class="chart">
					
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default {
		name: "Dashboard",
		created(){
			this.$parent.dashboard = true
		}
	}
</script>
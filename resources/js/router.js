import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './store.js'
import Home from './views/Home'
import PageNotFound from './views/PageNotFound'
import Preflight from './views/principal/Preflight'
import Principal from './views/Principal'
import AddPrincipal from './views/AddPrincipal'
import ViewPrincipal from './views/ViewPrincipal'
import ViewDependent from './views/dependent/ViewDependent'
import EditDependent from './views/dependent/EditDependent'
import AddDependent from './views/dependent/AddDependent'
import DependentsList from './views/dependent/DependentsList'
import Agencies from './views/Agencies'
import AddAgency from './views/AddAgency'
import ViewAgency from './views/agency/ViewAgency'
import EditAgency from './views/agency/EditAgency'
import Vehicles from './views/Vehicles'
import SearchClient from './views/iframe/SearchClient'
import SettingsIndex from './views/settings/SettingsIndex'
import DocumentTemplates from './views/settings/DocumentTemplates'
import DocumentTemplatesList from './views/settings/DocumentTemplatesList'
import DataMigration from './views/settings/DataMigration'
import IPMISFunctionality from './views/settings/IPMISFunctionality'
import BlanketVAT from './views/vat/BlanketVAT'
// BlanketVATBatch
import BlanketVATBatch from './views/vat/components/BlanketVATBatch'
import NormalVAT from './views/vat/components/NormalVAT'

// Vehicles
import VehicleHome from './views/vehicle/VehicleHome'
import VehicleList from './views/vehicle/VehicleList'
import Plates from './views/vehicle/Plates'
import PlatesList from './views/vehicle/plates/PlatesList'
import Prefix from './views/vehicle/plates/Prefix'
import PlatesOrder from './views/vehicle/plates/PlatesOrder'
import Tims from './views/vehicle/tims/Tims'
import TimsList from './views/vehicle/tims/TimsList'
import TimsRegistration from './views/vehicle/tims/TimsRegistration'
import RNPList from './views/vehicle/plates/rnp/RNPList'
import RNPCreate from './views/vehicle/plates/rnp/RNPCreate'
import RNPEdit from './views/vehicle/plates/rnp/RNPEdit'

// Data
import DataManagement from './views/data/DataManagement'

import Subprocesses from './views/processes/Subprocesses'

// Exports
import VATListExport from './views/exports/VATListExport'
import OrganizationDataExport from './views/exports/OrganizationDataExport'

import EditDomesticWorker from './views/domestic-worker/EditDomesticWorker'

import Clients from './views/other/Clients'
import AddOtherClient from './views/other/AddOtherClient'
import EditOtherClient from './views/other/EditOtherClient'

import AdobeSignSignatories from './views/settings/adobesign/AdobeSignSignatories'
import AddAdobeSignSignatory from './views/settings/adobesign/AddAdobeSignSignatory'
import AdobeSignDocuments from './views/settings/adobesign/AdobeSignDocuments'

import ReassignOldCases from './views/settings/ReassignOldCases'

import UserApplications from './views/settings/applications/UserApplications'
import AssignUserApplications from './views/settings/applications/AssignUserApplications'
import UserIndex from './views/settings/users/Index'
import AddUser from './views/settings/users/AddUser'

Vue.use(VueRouter)

let routes = [
 	{
 		path: '/',
 		name: 'home',
 		component: Home,
 		meta: {
 			auth: true,
 			title: 'Home',
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/principal',
 		name: 'principal',
 		component: Principal,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/principal/add-pre-flight',
 		name: 'principal.add.pre-flight',
 		component: Preflight,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/principal/add',
 		name: 'principal.add',
 		component: AddPrincipal,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/principal/view/:id',
 		name: 'principal.view',
 		component: ViewPrincipal
 	},
	{
 		path: '/dependent/view/:id',
 		name: 'dependent.view',
 		component: ViewDependent
 	},
 	{
 		path: '/dependent/edit/:id',
 		name: 'dependent.edit',
 		component: EditDependent,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/dependent/add/:id',
 		name: 'dependent.add',
 		component: AddDependent,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		name: 'dependents.list',
 		component: DependentsList,
 		path: '/dependents/list',
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/clients/other',
 		name: 'clients.other',
 		component: Clients,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/clients/other/add',
 		name: 'clients.other.add',
 		component: AddOtherClient,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/clients/other/edit/:id',
 		name: 'clients.other.edit',
 		component: EditOtherClient,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/agencies',
 		name: 'agencies',
 		component: Agencies,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/agencies/add',
 		name: 'agencies.add',
 		component: AddAgency,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/agencies/edit/:id',
 		name: 'agencies.edit',
 		component: EditAgency,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/agencies/view/:id',
 		name: 'agencies.view',
 		component: ViewAgency
 	},
 	{
 		path: '/domestic-worker/edit/:id',
 		name: 'domestic-worker.edit',
 		component: EditDomesticWorker,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/vehicles',
 		name: 'vehicles',
 		component: Vehicles,
        meta: {
            roles: ['Administrator', 'AdminAssistant', 'Supervisor'],
            auth: true
        },
 		children: [
 			{
 				path: '',
 				component: VehicleHome,
 				name: 'vehicle-home',
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
		 		}
 			},
 			{
 				path: '/list',
 				component: VehicleList,
 				name: 'vehicle-list',
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
		 		}
 			},
 			{
 				path: 'plates',
 				component: Plates,
 				name: 'vehicle-plates',
 				children: [
 					{
 						path: 'prefixes',
 						component: Prefix,
 						name: 'vehicle-plates-prefix',
				 		meta: {
				 			auth: true,
				 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
				 		}
 					},
 					{
 						path: 'list',
 						component: PlatesList,
 						name: 'vehicle-plates-list',
				 		meta: {
				 			auth: true,
				 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
				 		}
 					},
 					{
 						path: 'order',
 						component: PlatesOrder,
 						name: 'vehicle-plates-orders',
				 		meta: {
				 			auth: true,
				 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
				 		}
 					},
 					{
 						path: 'rnp/list',
 						component: RNPList,
 						name: 'rnp-list',
				 		meta: {
				 			auth: true,
				 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
				 		}
 					},
 					{
 						path: 'rnp/create',
 						component: RNPCreate,
 						name: 'rnp-create',
				 		meta: {
				 			auth: true,
				 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
				 		}
 					},
 					{
 						path: 'rnp/edit/:id',
 						component: RNPEdit,
 						name: 'rnp-edit',
				 		meta: {
				 			auth: true,
				 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
				 		}
 					}
 				]
 			},
 			{
 				path: 'tims',
 				component: Tims,
 				name: 'tims',
 				children: [
 					{
 						path: 'list',
		 				component: TimsList,
		 				name: 'tims-list',
				 		meta: {
				 			auth: true,
				 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
				 		}
 					},
 					{
 						path: 'registration',
 						component: TimsRegistration,
 						name: 'tims-registration',
				 		meta: {
				 			auth: true,
				 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
				 		}
 					}
 				]
 			}
 		]
 	},
 	{
 		path: '/applications/all',
 		name: 'user-applications.all',
 		component: UserApplications,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'Supervisor', 'FocalPoint', 'Client']
 		}
 	},
 	{
 		path: '/applications/assign/:id',
 		name: 'user-applications.assign',
 		component: AssignUserApplications,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'Supervisor']
 		}
 	},
 	{
 		path: '/client/search',
 		name: 'search-clients',
 		component: SearchClient
 	},
 	{
 		path: '/exports/vat-list',
 		name: 'export-vat-list',
 		component: VATListExport,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/exports/organization-data',
 		name: 'export-organization-data',
 		component: OrganizationDataExport,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/settings',
 		name: 'settings',
 		component: SettingsIndex,
 		children: [
 			{
 				path: 'document/templates',
 				component: DocumentTemplatesList,
 				name: "settings-templates",
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator']
		 		}
 			},
 			{
				path: 'document/templates/add',
				component: DocumentTemplates,
				name: 'settings-templates-add',
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator']
		 		}
			},
			{
				path: 'document/templates/edit/:id',
				component: DocumentTemplates,
				name: 'settings-templates-edit',
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator']
		 		}
			},
 			{
 				path: 'data/migration',
 				component: DataMigration,
 				name: "settings-migration",
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator']
		 		}
 			},
 			{
 				path: 'processmaker/ipmis/functionality',
 				component: IPMISFunctionality,
 				name: 'settings-ipmis-functionality',
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator', 'Supervisor']
		 		}
 			},
 			{
 				path: 'adobe-sign/signatories',
 				component: AdobeSignSignatories,
 				name: 'settings-signatories',
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator']
		 		}
 			},
 			{
 				path: 'adobe-sign/signatories/add',
 				component: AddAdobeSignSignatory,
 				name: 'setting-signatories-add',
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator']
		 		}
 			},
 			{
 				name: 'setting-signatories-edit',
 				path: 'adobe-sign/signatories/edit/:id',
 				component: AddAdobeSignSignatory,
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator']
		 		}
 			},
 			{
 				name: 'settings-reassign-cases',
 				path: 'reassign/oldcases',
 				component: ReassignOldCases,
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
		 		}
 			},
 			{
 				name: 'settings-adobesign-documents',
 				path: 'adobe-sign/documents',
 				component: AdobeSignDocuments,
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
		 		}
 			}
 		],
        meta: {
 		    roles: ['Administrator', 'Supervisor'],
            auth: true
        }
 	},
 	{
 		path: '/vat',
 		name: 'vat',
        meta: {
 		    roles: ['Administrator', 'AdminAssistant', 'Supervisor'],
            auth: true
        },
 		children: [
 			{
 				path: 'blanket',
 				component: BlanketVAT,
 				name: 'blanket',
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
		 		}
 			},
 			{
 				path: 'normal',
 				component: NormalVAT,
 				name: 'normal-vat',
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
		 		}
 			}
 		]
 	},
 	{
 		path: '/blanket-vat',
 		name: 'blanket-vat',
 		component: BlanketVAT,
        meta: {
 		    roles: ['Administrator', 'AdminAssistant', 'Supervisor'],
            auth: true
        },
 		children: [
 			{
 				path: 'batches',
 				component: BlanketVATBatch,
 				name: 'blanket-vat-batches',
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
		 		}
 			},
 			{
 				path: 'normal',
 				component: NormalVAT,
 				name: 'normal-vat-data',
		 		meta: {
		 			auth: true,
		 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
		 		}
 			}
 		]
 	},
 	{
 		path: '/data-management',
 		name: 'data-management',
 		component: DataManagement,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '/subprocesses/ipmis',
 		name: "subprocesses-ipmis",
 		component: Subprocesses,
 		meta: {
 			auth: true,
 			roles: ['Administrator', 'AdminAssistant', 'Supervisor']
 		}
 	},
 	{
 		path: '*',
 		component: PageNotFound,
 		name: '404'
 	},
	{
		name: 'users',
		path: '/users',
		component: UserIndex,
 		meta: {
 			auth: true,
 			roles: ['Administrator']
 		}
	},
	{
		name: 'user-add',
		path: '/users/add',
		component: AddUser,
 		meta: {
 			auth: true,
 			roles: ['Administrator']
 		}
	}
]

let router = new VueRouter({
	// mode: "history",
	linkExactActiveClass: "active",
	routes: routes ,
	scrollBehavior (to, from, savedPosition) {
		// Ensure that the page scrolls to the top after changing the route
		return { x: 0, y: 0 }
	}
})

router.beforeEach((to, from, next) => {
	// document.title = to.meta.title
    let loggedInUser = {}
    let response = false

    getUser().then(res => {
        loggedInUser = res
        console.log(res)
        if(loggedInUser && to.meta.auth){
            let roles = loggedInUser.roles
            let routeRoles = to.meta.roles

            let rolesArray = _.map(roles, (role) => {
                return role.name
            })

            let exists = rolesArray.some(role => {
                return _.includes(routeRoles, role)
            });

            if(exists){
                next()
            }else{
                console.log("Not allowed")
            }
        }else{
            if(loggedInUser){
                next()
            }
            else{
                if (to.query.type == "iframe"){
                    console.log("This is an iframe")
                    next({next: to.name})
                }else{
                    console.log("Not an Iframe")
                }
            }

        }
    })

})

async function getUser(){
    let user = {}
    try {
        user = (await axios.get('/api/auth/details'))
    }catch(error){
        console.log(error)
    }

    return user.data;
}

router.beforeResolve((to, from, next) => {
    if (to.name) {
        NProgress.start()
    }
    next()
});

router.afterEach((to, from) => {
	NProgress.done()
});

export default router

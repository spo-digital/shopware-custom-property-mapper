import './page/property-mapper.vue';

Shopware.Module.register('property-mapper', {
    type: 'plugin',
    name: 'Property Mapper',
    title: 'Eigenschafts-Abgleich',
    description: 'Startet den vollständigen Abgleich manuell.',

    routes: {
        index: {
            component: 'property-mapper',
            path: 'index'
        }
    },

    navigation: [{
        label: 'Eigenschafts-Abgleich',
        path: 'property-mapper.index',
        parent: 'sw-catalogue',
        position: 100,
        privilege: 'custom_property_mapper.viewer' // ⬅️ Wichtig!
    }],

    acl: {
        'custom_property_mapper.viewer': {
            label: 'Darf Eigenschafts-Abgleich nutzen',
            description: 'Erlaubt Zugriff auf das Admin-Modul für den manuellen Eigenschafts-Abgleich.'
        }
    }
});

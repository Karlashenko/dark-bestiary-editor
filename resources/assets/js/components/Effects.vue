<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-4">
                    <list :elements="effects" :fields="['id', 'name', 'type']" v-model="selected" :filters="getFilters()"></list>
                </div>

                <div class="col-md-8">
                    <div class="panel panel-default" v-if="selected">
                        <div class="panel-footer">
                            <div class="pull-left">
                                <button class="btn btn-primary btn-wide" @click="save(selected)"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
                                <button class="btn btn-danger btn-wide" @click="destroy(selected)"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-default" @click="duplicate(selected)"><span class="glyphicon glyphicon-duplicate"></span> Duplicate</button>
                                <button class="btn btn-default" @click="reset(selected)"><span class="glyphicon glyphicon-refresh"></span> Reset</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="panel-body">
                            <effect v-model="selected"></effect>
                        </div>

                        <div class="panel-footer">
                            <div class="pull-left">
                                <button class="btn btn-primary btn-wide" @click="save(selected)"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
                                <button class="btn btn-danger btn-wide" @click="destroy(selected)"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-default" @click="duplicate(selected)"><span class="glyphicon glyphicon-duplicate"></span> Duplicate</button>
                                <button class="btn btn-default" @click="reset(selected)"><span class="glyphicon glyphicon-refresh"></span> Reset</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { Events } from './../events';

    export default {
        data() {
            return {
                effects: [],
                window: window,
                selected: undefined,
            };
        },

        mounted() {
            this.load();

            Events.$off();
            Events.$on('toolbar.load', this.load);
            Events.$on('toolbar.create', this.create);
            Events.$on('toolbar.save', this.saveAll);
            Events.$on('toolbar.download', this.download);
        },

        methods: {
            getFilters: function () {
                return [
                    {
                        title: "Type",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.window.library.effectTypes
                        },
                        filter: (element, value) => {
                            return !value || element.type === value
                        },
                        value: null
                    },
                    {
                        title: "Damage Type",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.window.library.damageTypes
                        },
                        filter: (element, value) => {
                            return !value || element.damage_type === value
                        },
                        value: null
                    },
                    {
                        title: "Damage Flags",
                        component: "flags",
                        props: {
                            flags: window.library.damageFlags
                        },
                        filter: (element, value) => {
                            return !value || value.length === 0 || element.damage_flags.some(flag => value.indexOf(flag) >= 0);
                        },
                        value: null
                    }
                ];
            },

            load: function () {
                window.axios.get('/frontend/effects').then(response => {
                    this.effects = response.data;
                    this.select(this.id === undefined ? this.effects[0] : this.effects.find((element) => element.id === this.id));
                });
            },

            select: function (effect) {
                this.selected = effect;
            },

            create: function () {
                this.effects.push({
                    fresh: true,
                    name: "Just Another Effect " + this.effects.length,
                    type: window.library.effectTypes[0],
                    chance: 1,
                    validators: [],
                    attachments: [],
                    effects: [],
                    damage_flags: [],
                    damage_info_flags: [],
                    search_perimeter_sides: [],
                    dispell_behaviour_flags: [],
                    dispell_behaviour_status_flags: [],
                    dispell_behaviour_status_flags: [],
                    per_behaviour_stack_status_flags: [],
                });

                this.select(this.effects[this.effects.length - 1]);
            },

            duplicate: function (effect) {
                let copy = Object.assign({}, effect);
                copy.fresh = true;
                copy.id = '';
                copy.name = copy.name + " copy";

                this.effects.push(copy);
                this.select(copy);
            },

            reset: function (effect) {
                if (effect.fresh) {
                    return;
                }

                let index = this.effects.indexOf(effect);

                window.axios.get('/frontend/effects/' + effect.id).then(response => {
                    this.effects[index] = response.data;
                    this.select(this.effects[index]);
                });
            },

            destroy: function (effect) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.effects.indexOf(effect);

                if (effect.fresh) {
                    this.effects.splice(index, 1);
                    this.select(this.effects[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/effects/' + effect.id).then(response => {
                    this.effects.splice(index, 1);
                    this.select(this.effects[index <= 0 ? 0 : index - 1]);
                });
            },

            save: function (effect) {
                let index = this.effects.indexOf(effect);

                if (effect.fresh) {
                    window.axios.post('/frontend/effects', effect).then(response => {
                        this.effects[index] = response.data;
                        this.select(this.effects[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/effects/' + effect.id, effect).then(response => {
                    this.effects[index] = response.data;
                    this.select(this.effects[index]);
                });
            },

            saveAll: function () {
                for (let key in this.effects) {
                    let effect = this.effects[key];

                    if (effect.fresh) {
                        window.axios.post('/frontend/effects', effect);
                        continue;
                    }

                    window.axios.put('/frontend/effects/' + effect.id, effect);
                }

                this.load();
            }
        }
    };
</script>

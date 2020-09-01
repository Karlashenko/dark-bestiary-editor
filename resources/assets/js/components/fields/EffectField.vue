<template>
    <div>
        <table style="width: 100%; table-layout: auto;">
            <tr>
                <td style="padding-right: 4px; white-space: nowrap;">
                    <button class="btn btn-default" @click="create()">
                        <span class="glyphicon glyphicon-file"></span> New
                    </button>
                </td>

                <td style="width: 100%;">
                    <select class="form-control selectpicker" data-live-search="true" v-model="selected">
                        <option :value="null">NULL</option>
                        <option v-for="effect in effects" :value="effect" :selected="selected === effect">
                            {{ effect.name }}
                        </option>
                    </select>
                </td>
            </tr>
        </table>

        <button class="btn btn-default" style="margin: 4px 0; width: 100%;" @click="editMode = !editMode">
            <span :class="editMode ? 'glyphicon-chevron-up' : 'glyphicon-chevron-down'" class="glyphicon"></span>
        </button>

        <div class="panel panel-default" v-if="selected && editMode" style="margin-bottom: 4px;">
            <div class="panel-body">
                <effect v-model="selected"></effect>
            </div>

            <div class="panel-footer">
                <div class="pull-left">
                    <button class="btn btn-default btn-wide" @click="save(selected)"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
                    <button class="btn btn-default btn-wide" @click="destroy(selected)"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                </div>
                <div class="pull-right">
                    <button class="btn btn-default" @click="duplicate(selected)"><span class="glyphicon glyphicon-duplicate"></span> Duplicate</button>
                    <button class="btn btn-default" @click="reset(selected)"><span class="glyphicon glyphicon-refresh"></span> Reset</button>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["effects", "value"],

        data() {
            return {
                selected: this.effects.find((effect) => {return effect.id === this.value}),
                editMode: false,
                initialized: false
            };
        },

        mounted() {
            $(this.$el).find(".selectpicker").selectpicker("refresh");
        },

        updated() {
            if (this.initialized === false) {
                this.initialized = true;
                this.selected = this.effects.find((effect) => {return effect.id === this.value});
            }

            window.$(this.$el.querySelector("select")).selectpicker("refresh");
        },

        watch: {
            selected: function (value) {
                this.$emit("input", value ? value.id : undefined);
            },

            value: function (value) {
                this.selected = this.effects.find((effect) => {return effect.id === value});
            }
        },

        methods: {
            create: function () {
                this.selected = {
                    fresh: true,
                    effects: [],
                    attachments: [],
                    propertyModifiers: [],
                    validators: []
                };

                this.effects.push(this.selected);
            },

            destroy: function () {
                if (!confirm('Confirm delete?')) {
                    return;
                }

                this.effects.splice(this.effects.indexOf(this.selected), 1);

                if (this.selected.fresh === true) {
                    this.selected = undefined;
                    return;
                }

                window.axios.delete("/frontend/effects/" + this.selected.id);
                this.selected = undefined;
            },

            save: function (effect) {
                let index = this.effects.indexOf(effect);

                if (effect.fresh) {
                    window.axios.post("/frontend/effects", effect).then(response => {
                        this.effects[index] = response.data;
                        this.selected = this.effects[index];
                    });
                } else {
                    window.axios.put("/frontend/effects/" + effect.id, effect).then(response => {
                        this.effects[index] = response.data;
                        this.selected = this.effects[index];
                    });
                }
            },

            duplicate: function (effect) {
                let copy = Object.assign({fresh: true}, effect);
                copy.name += " (Copy)";
                this.effects.push(copy);
                this.selected = copy;
            },

            reset: function (effect) {
                if (effect.fresh) {
                    return;
                }

                let index = this.effects.indexOf(effect);

                window.axios.get('/frontend/effects/' + effect.id).then(response => {
                    this.effects[index] = response.data;
                    this.selected = this.effects[index];
                });
            },
        }
    };
</script>

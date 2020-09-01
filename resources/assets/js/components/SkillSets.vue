<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="sets" :fields="['id', 'name_i18n.en']" :icon="'icon'" v-model="selected"></list>
                </div>

                <div class="col-md-9">
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
                            <table class="form-table">
                                <tr>
                                    <td>Id</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.id" disabled>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Index</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.index">
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Name I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.name_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Icon</td>
                                    <td>
                                        <icon-field v-model="selected.icon"></icon-field>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Skills</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tbody>
                                            <tr v-for="skill in selected.skills">
                                                <td style="width: 80%;">
                                                    <skill-field v-model="skill.id" :skills="skills"></skill-field>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeSkill(selected, skill)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addSkill(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Behaviours</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tbody>
                                            <tr v-for="behaviour in selected.behaviours">
                                                <td>
                                                    <input type="number" class="form-control" v-model="behaviour.pivot.skill_count">
                                                </td>

                                                <td style="width: 80%;">
                                                    <behaviour-field v-model="behaviour.id" :behaviours="behaviours"></behaviour-field>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeBehaviour(selected, behaviour)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addBehaviour(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
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
    import {Events} from './../events';

    export default {
        data() {
            return {
                sets: [],
                skills: [],
                behaviours: [],
                window: window,
                selected: undefined
            };
        },

        mounted() {
            Events.$off();
            Events.$on('toolbar.load', this.load);
            Events.$on('toolbar.create', this.create);
            Events.$on('toolbar.save', this.saveAll);
            Events.$on('toolbar.download', this.download);

            this.load();
        },

        updated () {
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        methods: {
            load: function () {
                window.axios.get('/frontend/skill_sets').then(response => {
                    this.sets = response.data;
                    this.select(this.id === undefined ? this.sets[0] : this.sets.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/behaviours').then(response => {
                    this.behaviours = response.data;
                });

                window.axios.get('/frontend/skills').then(response => {
                    this.skills = response.data;
                });
            },

            create: function () {
                this.sets.push({
                    fresh: true,
                    skills: [],
                    behaviours: [],
                    name_i18n: {en: "Just Another Skill Set"}
                });

                this.select(this.sets[this.sets.length - 1]);
            },

            select: function (type) {
                this.selected = type;
            },

            duplicate: function (type) {
                let copy = Object.assign({}, type);
                copy.fresh = true;
                this.sets.push(copy);
                this.select(copy);
            },

            addBehaviour: function (set) {
                set.behaviours.push({
                    id: this.behaviours[0] ? this.behaviours[0].id : undefined,
                    pivot: {item_count: 0}
                });
            },

            removeBehaviour: function (set, behaviour) {
                let index = set.behaviours.indexOf(behaviour);
                set.behaviours.splice(index, 1);
            },

            addSkill: function (set) {
                set.skills.push({
                    id: this.skills[0] ? this.skills[0].id : undefined
                });
            },

            removeSkill: function (set, skill) {
                let index = set.skills.indexOf(skill);
                set.skills.splice(index, 1);
            },

            reset: function (type) {
                if (type.fresh) {
                    return;
                }

                let index = this.sets.indexOf(type);

                window.axios.get('/frontend/skill_sets/' + type.id).then(response => {
                    this.sets[index] = response.data;
                    this.select(this.sets[index]);
                });
            },

            save: function (type) {
                let index = this.sets.indexOf(type);

                if (type.fresh) {
                    window.axios.post('/frontend/skill_sets', type).then(response => {
                        this.sets[index] = response.data;
                        this.select(this.sets[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/skill_sets/' + type.id, type).then(response => {
                    this.sets[index] = response.data;
                    this.select(this.sets[index]);
                });
            },

            destroy: function (type) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.sets.indexOf(type);

                if (type.fresh) {
                    this.sets.splice(index, 1);
                    this.select(this.sets[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/skill_sets/' + type.id).then(() => {
                    this.sets.splice(index, 1);
                    this.select(this.sets[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.sets) {
                    let type = this.sets[key];

                    if (type.fresh) {
                        window.axios.post('/frontend/skill_sets', type).then(response => {
                            this.sets[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/skill_sets/' + type.id, type).then(response => {
                        this.sets[key] = response.data;
                    });
                }
            }
        }
    };
</script>

<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="backgrounds" :fields="['id', 'name_i18n.en']" v-model="selected" :filters="getFilters()"></list>
                </div>

                <div class="col-md-9">
                    <div class="panel panel-default" v-if="selected">
                        <div class="panel-body">
                            <table class="form-table">
                                <tr>
                                    <td>Enabled</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_enabled"/>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Id</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.id" disabled>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Name I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.name_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Description I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.description_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Gold</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.gold">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Items</td>
                                    <td>
                                        <table class="table-inner">
                                            <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Count</th>
                                                <th>Equipped</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="item in selected.items">
                                                <td style="width: 60%;">
                                                    <item-field :items="items" v-model="item.id"></item-field>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="item.pivot.count">
                                                </td>

                                                <td style="text-align: center;">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" v-model="item.pivot.is_equipped">
                                                    </div>
                                                </td>

                                                <td>
                                                    <button class="btn btn-default" @click="removeItem(selected, item)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="9">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addItem(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Skills</td>
                                    <td>
                                        <table class="table-inner">
                                            <thead>
                                            <tr>
                                                <th>Skill</th>
                                                <th>Type</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="skill in selected.skills">
                                                <td style="width: 60%;">
                                                    <skill-field :skills="skills" v-model="skill.id"></skill-field>
                                                </td>

                                                <td style="text-align: center;">
                                                    <p>{{ skill.type }}</p>
                                                </td>

                                                <td>
                                                    <button class="btn btn-default" @click="removeSkill(selected, skill)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="9">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addSkill(selected)">
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
    import {Events} from "./../events";

    export default {
        data() {
            return {
                backgrounds: [],
                items: [],
                skills: [],
                selected: undefined
            };
        },

        mounted() {
            Events.$off();
            Events.$on("toolbar.load", this.load);
            Events.$on("toolbar.create", this.create);
            Events.$on("toolbar.save", this.saveAll);
            Events.$on("toolbar.download", this.download);

            this.load();
        },

        methods: {
            getFilters: function () {
                return [
                    {
                        title: "Enabled",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: [true, false]
                        },
                        filter: (element, value) => {
                            return value == null || element.is_enabled == value;
                        },
                        value: true
                    }
                ];
            },

            addItem: function (background) {
                background.items.push({
                    id: this.items[0].id,
                    pivot: {
                        count: 0,
                        is_equipped: false
                    }
                });
            },

            removeItem: function (background, item) {
                let index = background.items.indexOf(item);
                background.items.splice(index, 1);
            },

            addSkill: function (background) {
                background.skills.push({
                    id: this.skills[0].id
                });
            },

            removeSkill: function (background, skill) {
                let index = background.skills.indexOf(skill);
                background.skills.splice(index, 1);
            },

            create: function () {
                this.backgrounds.push({
                    fresh: true,
                    name_i18n: {en: "background_"},
                    items: []
                });

                this.select(this.backgrounds[this.backgrounds.length - 1]);
            },

            saveAll: function () {
                for (let key in this.backgrounds) {
                    let background = this.backgrounds[key];

                    if (background.fresh) {
                        window.axios.post("/frontend/backgrounds", background).then(response => {
                            this.backgrounds[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put("/frontend/backgrounds/" + background.id, background).then(response => {
                        this.backgrounds[key] = response.data;
                    });
                }
            },

            load: function () {
                window.axios.get("/frontend/backgrounds").then(response => {
                    this.backgrounds = response.data;
                    this.select(this.id === undefined ? this.backgrounds[0] : this.backgrounds.find((element) => element.id === this.id));
                });

                window.axios.get("/frontend/items").then(response => {
                    this.items = response.data;
                });

                window.axios.get("/frontend/skills").then(response => {
                    this.skills = response.data;
                });
            },

            select: function (background) {
                this.selected = background;
            },

            duplicate: function (background) {
                let copy = Object.assign({}, background);
                copy.fresh = true;
                this.backgrounds.push(copy);
                this.select(copy);
            },

            reset: function (background) {
                if (background.fresh) {
                    return;
                }

                let index = this.backgrounds.indexOf(background);

                window.axios.get("/frontend/backgrounds/" + background.id).then(response => {
                    this.backgrounds[index] = response.data;
                    this.select(this.backgrounds[index]);
                });
            },

            save: function (background) {
                let index = this.backgrounds.indexOf(background);

                if (background.fresh) {
                    window.axios.post("/frontend/backgrounds", background).then(response => {
                        this.backgrounds[index] = response.data;
                        this.select(this.backgrounds[index]);
                    });

                    return;
                }

                window.axios.put("/frontend/backgrounds/" + background.id, background).then(response => {
                    this.backgrounds[index] = response.data;
                    this.select(this.backgrounds[index]);
                });
            },

            destroy: function (background) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.backgrounds.indexOf(background);

                if (background.fresh) {
                    this.backgrounds.splice(index, 1);
                    this.select(this.backgrounds[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete("/frontend/backgrounds/" + background.id).then(response => {
                    this.backgrounds.splice(index, 1);
                    this.select(this.backgrounds[index <= 0 ? 0 : index - 1]);
                });
            }
        }
    };
</script>

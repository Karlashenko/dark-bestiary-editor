<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="skins" :fields="['id', 'label']" v-model="selected"></list>
                </div>

                <div class="col-md-9">
                    <div class="panel panel-default" v-if="selected">
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
                                    <td>Label</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.label">
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Preset</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="preset" v-on:input="updatePreset" :options="['Armor', 'Boots', 'Gloves', 'Pants']"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Parts</td>
                                    <td>
                                        <table class="table-inner">
                                            <tr v-for="part in selected.parts">
                                                <td>
                                                    <selectpicker v-model="part.Part" :options="window.library.skinParts"></selectpicker>
                                                </td>

                                                <td>
                                                    <mesh-field v-model="part.Mesh"></mesh-field>
                                                </td>

                                                <td>
                                                    <button class="btn btn-default" @click="removePart(selected, part)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="2">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addPart(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
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
                skins: [],
                preset: [],
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

        methods: {
            updatePreset: function() {
                switch (this.preset) {
                    case "Armor":
                        this.selected.parts = [
                            {Part: "ArmorCenter", Mesh: ""},
                            {Part: "ArmorLeft01", Mesh: ""},
                            {Part: "ArmorLeft02", Mesh: ""},
                            {Part: "ArmorRight01", Mesh: ""},
                            {Part: "ArmorRight02", Mesh: ""},
                        ]
                        break;
                    case "Boots":
                        this.selected.parts = [
                            {Part: "BootsLeft", Mesh: ""},
                            {Part: "BootsRight", Mesh: ""},
                        ]
                        break;
                    case "Gloves":
                        this.selected.parts = [
                            {Part: "GlovesLeft", Mesh: ""},
                            {Part: "GlovesRight", Mesh: ""},
                        ]
                        break;
                    case "Pants":
                        this.selected.parts = [
                            {Part: "PantsCenter", Mesh: ""},
                            {Part: "PantsLeft01", Mesh: ""},
                            {Part: "PantsLeft02", Mesh: ""},
                            {Part: "PantsRight01", Mesh: ""},
                            {Part: "PantsRight02", Mesh: ""},
                        ]
                        break;
                }
            },

            addPart: function (skin) {
                skin.parts.push({
                    Part: "",
                    Mesh: "",
                });
            },

            removePart: function (skin, part) {
                let index = skin.parts.indexOf(part);
                skin.parts.splice(index, 1);
            },

            load: function () {
                window.axios.get('/frontend/skins').then(response => {
                    this.skins = response.data;
                    this.select(this.id === undefined ? this.skins[0] : this.skins.find((element) => element.id === this.id));
                });
            },

            create: function () {
                this.skins.push({
                    fresh: true,
                    parts: {},
                    label: "skin_"
                });

                this.select(this.skins[this.skins.length - 1]);
            },

            select: function (skin) {
                this.selected = skin;
            },

            duplicate: function (skin) {
                let copy = Object.assign({}, skin);
                copy.fresh = true;
                this.skins.push(copy);
                this.select(copy);
            },

            reset: function (skin) {
                if (skin.fresh) {
                    return;
                }

                let index = this.skins.indexOf(skin);

                window.axios.get('/frontend/skins/' + skin.id).then(response => {
                    this.skins[index] = response.data;
                    this.select(this.skins[index]);
                });
            },

            save: function (skin) {
                let index = this.skins.indexOf(skin);

                if (skin.fresh) {
                    window.axios.post('/frontend/skins', skin).then(response => {
                        this.skins[index] = response.data;
                        this.select(this.skins[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/skins/' + skin.id, skin).then(response => {
                    this.skins[index] = response.data;
                    this.select(this.skins[index]);
                });
            },

            destroy: function (skin) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.skins.indexOf(skin);

                if (skin.fresh) {
                    this.skins.splice(index, 1);
                    this.select(this.skins[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/skins/' + skin.id).then(() => {
                    this.skins.splice(index, 1);
                    this.select(this.skins[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.skins) {
                    let skin = this.skins[key];

                    if (skin.fresh) {
                        window.axios.post('/frontend/skins', skin).then(response => {
                            this.skins[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/skins/' + skin.id, skin).then(response => {
                        this.skins[key] = response.data;
                    });
                }
            }
        }
    };
</script>

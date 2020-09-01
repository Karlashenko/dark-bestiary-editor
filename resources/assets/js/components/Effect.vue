<template>
    <div>
        <div v-if="selected" :id="'effect_' + selected.id">
            <table class="form-table">
                <tr>
                    <td>Id</td>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control" v-model="selected.id" disabled />
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Name</td>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control" v-model="selected.name">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Type</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="selected.type" :options="library.effectTypes"></selectpicker>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Target</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="selected.target" :options="library.effectTargetTypes"></selectpicker>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Sound</td>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control" v-model="selected.sound">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Animation</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="selected.animation" :nullable="true" :options="library.animations"></selectpicker>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Chance</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" min="0" max="1" step="0.01" v-model="selected.chance">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Stack Chance</td>
                    <td>
                        <div class="form-group">
                            <input type="checkbox" v-model="selected.stack_chance">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Caster is owner</td>
                    <td>
                        <div class="form-group">
                            <input type="checkbox" v-model="selected.caster_is_owner">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Validators</td>
                    <td>
                        <table class="table-inner">
                            <thead>
                            <tr>
                                <th>Validator</th>
                                <th>Purpose</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tr v-for="validator in selected.validators">
                                <td>
                                    <selectpicker v-model="validator.pivot.validator_id"
                                                  :options="validators"
                                                  :valuecallback="(element) => element.id"
                                                  :labelcallback="(element) => element.name">
                                    </selectpicker>
                                </td>

                                <td>
                                    <selectpicker v-model="validator.pivot.validator_purpose"
                                                  :options="library.validatorPurposes"
                                                  :nullable="true">
                                    </selectpicker>
                                </td>

                                <td style="padding-left: 4px; vertical-align: top; white-space: nowrap;">
                                    <button class="btn btn-default" @click="validatorMoveUp(selected, validator)">
                                        <span class="glyphicon glyphicon-arrow-up"></span>
                                    </button>

                                    <button class="btn btn-default" @click="validatorMoveDown(selected, validator)">
                                        <span class="glyphicon glyphicon-arrow-down"></span>
                                    </button>

                                    <button class="btn btn-default" @click="removeValidator(selected, validator)">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                </td>
                            </tr>

                            <tr style="height: 50px;">
                                <td colspan="3">
                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addValidator(selected)">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>Attachments</td>
                    <td>
                        <table class="table-inner">
                            <thead>
                            <tr>
                                <th>Target</th>
                                <th>Point</th>
                                <th>Prefab</th>
                                <th>Rotate</th>
                                <th>Rotate Same As Caster</th>
                                <th>Flip Rotation</th>
                                <th>Original Scale</th>
                            </tr>
                            </thead>
                            <tr v-for="attachment in selected.attachments">
                                <td>
                                    <selectpicker v-model="attachment.target" :options="library.subjects"></selectpicker>
                                </td>

                                <td>
                                    <selectpicker v-model="attachment.point" :options="library.attachmentPoints"></selectpicker>
                                </td>

                                <td>
                                    <prefab-field v-model="attachment.prefab"></prefab-field>
                                </td>

                                <td style="text-align: center">
                                    <div class="form-group">
                                        <input type="checkbox" v-model="attachment.rotate">
                                    </div>
                                </td>

                                <td style="text-align: center">
                                    <div class="form-group">
                                        <input type="checkbox" v-model="attachment.rotate_same_as_caster">
                                    </div>
                                </td>

                                <td style="text-align: center">
                                    <div class="form-group">
                                        <input type="checkbox" v-model="attachment.flip_rotation">
                                    </div>
                                </td>

                                <td style="text-align: center">
                                    <div class="form-group">
                                        <input type="checkbox" v-model="attachment.original_scale">
                                    </div>
                                </td>

                                <td>
                                    <button class="btn btn-default" @click="removeAttachment(selected, attachment)">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                </td>
                            </tr>

                            <tr style="height: 50px;">
                                <td colspan="3">
                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addAttachment(selected)">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- DAMAGE -->
            <table class="form-table" :class="selected.type !== 'MirrorImageEffect' ? 'hidden' : ''">
                <tr>
                    <td>Behaviour</td>
                    <td>
                        <behaviour-field v-model="selected.mirror_image_behaviour_id" :behaviours="behaviours"></behaviour-field>
                    </td>
                </tr>

                <tr>
                    <td>Count</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.mirror_image_count">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Duration</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.mirror_image_duration">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Prefab</td>
                    <td>
                        <div class="form-group">
                            <prefab-field v-model="selected.mirror_image_prefab"></prefab-field>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- REMOVE EQUIPPED ITEM -->
            <table class="form-table" :class="selected.type !== 'DestroyEquippedItemEffect' ? 'hidden' : ''">
                <tr>
                    <td>Item</td>
                    <td>
                        <div class="form-group">
                            <item-field :items="items" v-model="selected.destroy_equipped_item_id"></item-field>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- HOOK -->
            <table class="form-table" :class="selected.type !== 'HookEffect' ? 'hidden' : ''">
                <tr>
                    <td>Beam</td>
                    <td>
                        <div class="form-group">
                            <prefab-field v-model="selected.hook_beam"></prefab-field>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Hook</td>
                    <td>
                        <div class="form-group">
                            <prefab-field v-model="selected.hook_hook"></prefab-field>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- PER BEHAVIOUR STACK DAMAGE -->
            <table class="form-table" :class="selected.type !== 'PerBehaviourStackDamageEffect' ? 'hidden' : ''">
                <tr>
                    <td>Behaviour</td>
                    <td>
                        <behaviour-field v-model="selected.per_behaviour_stack_damage_behaviour_id" :behaviours="behaviours"></behaviour-field>
                    </td>
                </tr>

                <tr>
                    <td>Subject</td>
                    <td>
                        <selectpicker v-model="selected.per_behaviour_stack_damage_subject" :options="library.damageTargets"></selectpicker>
                    </td>
                </tr>

                <tr>
                    <td>Status Flags</td>
                    <td>
                        <flags v-model="selected.per_behaviour_stack_status_flags" :flags="library.behaviourStatusFlags"></flags>
                    </td>
                </tr>
            </table>

            <!-- DAMAGE -->
            <table class="form-table" :class="['DamageEffect', 'WeaponDamageEffect', 'PerBehaviourStackDamageEffect', 'RandomElementDamageEffect'].includes(selected.type) ? '' : 'hidden'">

                <tr>
                    <td>Type</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="selected.damage_type" :nullable="true" :options="library.damageTypes"></selectpicker>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Weapon Sound Type</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="selected.damage_weapon_sound_type" :nullable="true" :options="library.weaponSoundTypes"></selectpicker>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Base</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.damage_base">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Variance</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.damage_variance">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Vampirism</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.damage_vampirism">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Flags</td>
                    <td>
                        <flags v-model="selected.damage_flags" :flags="library.damageFlags"></flags>
                    </td>
                </tr>

                <tr>
                    <td>Info Flags</td>
                    <td>
                        <flags v-model="selected.damage_info_flags" :flags="library.damageInfoFlags"></flags>
                    </td>
                </tr>
            </table>

            <!-- HEAL -->
            <table class="form-table" :class="selected.type !== 'HealEffect' ? 'hidden' : ''">
                <tr>
                    <td>Base</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.heal_base">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Heal Flags</td>
                    <td>
                        <flags v-model="selected.heal_flags" :flags="library.healFlags"></flags>
                    </td>
                </tr>
            </table>

            <!-- SUCK IN -->
            <table class="form-table" :class="selected.type !== 'SuckInEffect' ? 'hidden' : ''">
                <tr>
                    <td>Radius</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.suck_in_radius">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Duration</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.suck_in_duration">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Animation</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="selected.suck_in_animation" :nullable="true" :options="library.animations"></selectpicker>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- REPEAT -->
            <table class="form-table" :class="selected.type !== 'RepeatEffect' ? 'hidden' : ''">
                <tr>
                    <td>Times</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.repeat_times">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Effect</td>
                    <td>
                        <div class="form-group">
                            <effect-field v-model="selected.repeat_effect_id" :effects="effects"></effect-field>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- CHAIN -->
            <table class="form-table" :class="selected.type !== 'ChainEffect' ? 'hidden' : ''">
                <tr>
                    <td>Times</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.chain_times">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Period</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.chain_period">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Radius</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.chain_radius">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Effect</td>
                    <td>
                        <div class="form-group">
                            <effect-field v-model="selected.chain_effect_id" :effects="effects"></effect-field>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Final Effect</td>
                    <td>
                        <div class="form-group">
                            <effect-field v-model="selected.chain_final_effect_id" :effects="effects"></effect-field>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- SHIELD -->
            <table class="form-table" :class="selected.type !== 'ShieldEffect' ? 'hidden' : ''">
                <tr>
                    <td>Behaviour</td>
                    <td>
                        <div class="form-group">
                            <behaviour-field v-model="selected.shield_behaviour_id" :behaviours="behaviours"></behaviour-field>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Base</td>
                    <td>
                        <div class="form-group">
                            <input class="form-control" type="number" v-model="selected.shield_base">
                        </div>
                    </td>
                </tr>
            </table>

            <!-- IF ELSE -->
            <table class="form-table" :class="selected.type !== 'IfElseEffect' ? 'hidden' : ''">
                <tr>
                    <td>If Effect</td>
                    <td>
                        <div class="form-group">
                            <effect-field v-model="selected.if_else_if_effect_id" :effects="effects"></effect-field>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Else Effect</td>
                    <td>
                        <div class="form-group">
                            <effect-field v-model="selected.if_else_else_effect_id" :effects="effects"></effect-field>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- REWARD -->
            <table class="form-table" :class="selected.type !== 'RewardEffect' ? 'hidden' : ''">
                <tr>
                    <td>Reward</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="selected.reward_id"
                                          :options="rewards"
                                          :valuecallback="(reward) => reward.id"
                                          :labelcallback="(reward) => reward.label">
                            </selectpicker>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- SET -->
            <table class="form-table" style="width: 100%; table-layout: auto;" :class="selected.type === 'EffectSet' || selected.type === 'RandomEffect' || selected.type === 'DamageEffect' || selected.type === 'WeaponDamageEffect' ? '' : 'hidden'">
                <tr>
                    <td style="white-space: nowrap;">Effects</td>
                    <td style="width: 100%">
                        <table style="width: 100%; table-layout: auto;">
                            <tr v-for="effect in selected.effects">
                                <td style="width: 100%;">
                                    <effect-field v-model="effect.id" :effects="effects"></effect-field>
                                </td>

                                <td style="padding-left: 4px; vertical-align: top; white-space: nowrap;">
                                    <button class="btn btn-default" @click="effectMoveUp(selected, effect)">
                                        <span class="glyphicon glyphicon-arrow-up"></span>
                                    </button>

                                    <button class="btn btn-default" @click="effectMoveDown(selected, effect)">
                                        <span class="glyphicon glyphicon-arrow-down"></span>
                                    </button>

                                    <button class="btn btn-default" @click="removeEffect(selected, effect)">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                </td>
                            </tr>

                            <tr style="height: 50px;">
                                <td>
                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addEffect(selected)">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- SEARCH POINTS -->
            <table class="form-table" :class="selected.type !== 'SearchPointsEffect' ? 'hidden' : ''">
                <tr>
                    <td>Unoccupied</td>
                    <td>
                        <div class="form-group">
                            <input type="checkbox" v-model="selected.search_points_unoccupied">
                        </div>
                    </td>
                </tr>
            </table>

            <!-- SEARCH LINE -->
            <table class="form-table" :class="selected.type !== 'SearchLineEffect' ? 'hidden' : ''">
                <tr>
                    <td>Length</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.search_line_length">
                    </td>
                </tr>

                <tr>
                    <td>Effect</td>
                    <td>
                        <effect-field v-model="selected.search_line_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>

                <tr>
                    <td>Check Line of Sight</td>
                    <td>
                        <div class="form-group">
                            <input type="checkbox" v-model="selected.search_line_check_line_of_sight">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Exclude Origin</td>
                    <td>
                        <div class="form-group">
                            <input type="checkbox" v-model="selected.search_line_exclude_origin">
                        </div>
                    </td>
                </tr>
            </table>

            <!-- SEARCH BEHIND -->
            <table class="form-table" :class="selected.type !== 'SearchBehindEffect' ? 'hidden' : ''">
                <tr>
                    <td>Effect</td>
                    <td>
                        <effect-field v-model="selected.search_behind_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>
            </table>

            <!-- LAUNCH AOE MISSILE -->
            <table class="form-table" :class="selected.type === 'LaunchAOEMissileEffect' ? '' : 'hidden'">
                <td>AOE Missile Radius</td>
                <td>
                    <div class="form-group">
                        <input type="number" class="form-control" v-model="selected.launch_aoe_missile_radius">
                    </div>
                </td>

                <tr>
                    <td>AOE Missile Effect</td>
                    <td>
                        <effect-field v-model="selected.launch_aoe_missile_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>
            </table>

            <!-- LAUNCH MISSILE -->
            <table class="form-table" :class="selected.type === 'LaunchMissileEffect' || selected.type === 'LaunchMissileFromTargetEffect' || selected.type === 'LaunchWeaponMissileEffect' || selected.type === 'LaunchAOEMissileEffect' ? '' : 'hidden'">
                <tr>
                    <td>Missile</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="selected.launch_missile_missile_id"
                                          :options="missiles"
                                          :valuecallback="(missile) => missile.id"
                                          :labelcallback="(missile) => missile.name">
                            </selectpicker>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Caster Attachment Point</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="selected.launch_missile_caster_attachment_point" :options="library.attachmentPoints"></selectpicker>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Caster Offset X</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.launch_missile_caster_offset_x">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Caster Offset Y</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.launch_missile_caster_offset_y">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Caster Directional Offset X</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.launch_missile_caster_directional_offset_x">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Caster Directional Offset Y</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.launch_missile_caster_directional_offset_y">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Target Offset X</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.launch_missile_target_offset_x">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Target Offset Y</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.launch_missile_target_offset_y">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Target Directional Offset X</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.launch_missile_target_directional_offset_x">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Target Directional Offset Y</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.launch_missile_target_directional_offset_y">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Fly Height</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.launch_missile_fly_height">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Target Attachment Point</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="selected.launch_missile_target_attachment_point" :options="library.attachmentPoints"></selectpicker>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Mover</td>
                    <td>
                        <div class="form-group">
                            <selectpicker :options="library.moverTypes" v-model="selected.launch_missile_mover_type"></selectpicker>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Speed</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.launch_missile_mover_speed">
                    </td>
                </tr>

                <tr>
                    <td>Acceleration</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.launch_missile_mover_acceleration">
                    </td>
                </tr>

                <tr>
                    <td>Height</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.launch_missile_mover_height">
                    </td>
                </tr>

                <tr>
                    <td>Finish Immediately</td>
                    <td>
                        <input type="checkbox" v-model="selected.launch_missile_finish_immediately">
                    </td>
                </tr>

                <tr>
                    <td>Is Piercing</td>
                    <td>
                        <input type="checkbox" v-model="selected.launch_missile_is_piercing">
                    </td>
                </tr>

                <tr>
                    <td>Rotate</td>
                    <td>
                        <input type="checkbox" v-model="selected.launch_missile_mover_rotate">
                    </td>
                </tr>

                <tr>
                    <td>Collide with environment</td>
                    <td>
                        <effect-field v-model="selected.launch_missile_collide_with_environment_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>

                <tr>
                    <td>Collide with entities</td>
                    <td>
                        <effect-field v-model="selected.launch_missile_collide_with_entities_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>

                <tr>
                    <td>Enter cell</td>
                    <td>
                        <effect-field v-model="selected.launch_missile_enter_cell_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>

                <tr>
                    <td>Exit cell</td>
                    <td>
                        <effect-field v-model="selected.launch_missile_exit_cell_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>

                <tr>
                    <td>Final Effect</td>
                    <td>
                        <effect-field v-model="selected.launch_missile_final_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>
            </table>

            <!-- KNOCKBACK -->
            <table class="form-table" :class="selected.type === 'KnockbackEffect' ? '' : 'hidden'">
                <tr>
                    <td>Distance</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.knockback_distance">
                    </td>
                </tr>

                <tr>
                    <td>Mover</td>
                    <td>
                        <div class="form-group">
                            <selectpicker :options="library.moverTypes" v-model="selected.knockback_mover_type"></selectpicker>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Speed</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.knockback_mover_speed">
                    </td>
                </tr>

                <tr>
                    <td>Height</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.knockback_mover_height">
                    </td>
                </tr>

                <tr>
                    <td>Collide with environment</td>
                    <td>
                        <effect-field v-model="selected.knockback_collide_with_environment_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>

                <tr>
                    <td>Collide with entities</td>
                    <td>
                        <effect-field v-model="selected.knockback_collide_with_entities_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>

                <tr>
                    <td>Final Effect</td>
                    <td>
                        <effect-field v-model="selected.knockback_final_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>
            </table>

            <!-- APPLY BEHAVIOUR -->
            <table class="form-table" :class="selected.type !== 'ApplyBehaviourEffect' ? 'hidden' : ''">
                <tr>
                    <td>Behaviour</td>
                    <td>
                        <behaviour-field v-model="selected.apply_behaviour_behaviour_id" :behaviours="behaviours"></behaviour-field>
                    </td>
                </tr>

                <tr>
                    <td>Stacks</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.apply_behaviour_stacks">
                    </td>
                </tr>
            </table>

            <!-- REMOVE BEHAVIOUR -->
            <table class="form-table" :class="selected.type !== 'RemoveBehaviourEffect' ? 'hidden' : ''">
                <tr>
                    <td>Behaviour</td>
                    <td>
                        <behaviour-field v-model="selected.remove_behaviour_behaviour_id" :behaviours="behaviours"></behaviour-field>
                    </td>
                </tr>
            </table>

            <!-- DRAG -->
            <table class="form-table" :class="selected.type !== 'DragEffect' ? 'hidden' : ''">
                <tr>
                    <td>Start Animation</td>
                    <td>
                        <selectpicker v-model="selected.drag_start_animation" :options="library.animations"></selectpicker>
                    </td>
                </tr>

                <tr>
                    <td>End Animation</td>
                    <td>
                        <selectpicker v-model="selected.drag_end_animation" :options="library.animations"></selectpicker>
                    </td>
                </tr>

                <tr>
                    <td>Mover Type</td>
                    <td>
                        <selectpicker v-model="selected.drag_mover_type" :options="library.moverTypes"></selectpicker>
                    </td>
                </tr>

                <tr>
                    <td>Mover Speed</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.drag_mover_speed">
                    </td>
                </tr>

                <tr>
                    <td>Mover Height</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.drag_mover_height">
                    </td>
                </tr>

                <tr>
                    <td>Stop On Collision</td>
                    <td>
                        <input type="checkbox" v-model="selected.drag_stop_on_collision">
                    </td>
                </tr>

                <tr>
                    <td>Is Airborne</td>
                    <td>
                        <input type="checkbox" v-model="selected.drag_is_airborne">
                    </td>
                </tr>

                <tr>
                    <td>Collide with environment effect</td>
                    <td>
                        <effect-field v-model="selected.drag_collide_with_environment_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>

                <tr>
                    <td>Collide with entities effect</td>
                    <td>
                        <effect-field v-model="selected.drag_collide_with_entities_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>

                <tr>
                    <td>Enter cell effect</td>
                    <td>
                        <effect-field v-model="selected.drag_enter_cell_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>

                <tr>
                    <td>Exit cell effect</td>
                    <td>
                        <effect-field v-model="selected.drag_exit_cell_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>

                <tr>
                    <td>Final Effect</td>
                    <td>
                        <effect-field v-model="selected.drag_final_effect_id" :effects="effects"></effect-field>
                    </td>
                </tr>
            </table>

            <!-- RESTORE RESOURCE -->
            <table class="form-table" :class="selected.type !== 'RestoreResourceEffect' ? 'hidden' : ''">
                <tr>
                    <td>Resource Type</td>
                    <td>
                        <selectpicker v-model="selected.restore_resource_type" :options="library.resourceTypes"></selectpicker>
                    </td>
                </tr>

                <tr>
                    <td>Resource Amount</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.restore_resource_amount">
                    </td>
                </tr>
            </table>

            <!-- RUN COOLDOWN -->
            <table class="form-table" :class="selected.type !== 'RunCooldownEffect' ? 'hidden' : ''">
                <tr>
                    <td>Skill</td>
                    <td>
                        <skill-field v-model="selected.run_cooldown_skill_id" :skills="skills"></skill-field>
                    </td>
                </tr>
            </table>

            <!-- REDUCE COOLDOWNS -->
            <table class="form-table" :class="selected.type !== 'ReduceCooldownsEffect' ? 'hidden' : ''">
                <tr>
                    <td>Amount</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.reduce_cooldowns_amount">
                    </td>
                </tr>
            </table>

            <!-- RUN AWAY -->
            <table class="form-table" :class="selected.type !== 'RunAwayEffect' ? 'hidden' : ''">
                <tr>
                    <td>Animation</td>
                    <td>
                        <selectpicker v-model="selected.move_animation" :options="library.animations"></selectpicker>
                    </td>
                </tr>

                <tr>
                    <td>Speed</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.move_speed">
                    </td>
                </tr>

                <tr>
                    <td>Distance</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.move_distance">
                    </td>
                </tr>

                <tr>
                    <td>Random Direction</td>
                    <td>
                        <input type="checkbox" v-model="selected.move_random_direction">
                    </td>
                </tr>

                <tr>
                    <td>To Caster</td>
                    <td>
                        <input type="checkbox" v-model="selected.move_to_caster">
                    </td>
                </tr>

                <tr>
                    <td>To Target</td>
                    <td>
                        <input type="checkbox" v-model="selected.move_to_target">
                    </td>
                </tr>
            </table>

            <!-- DISPELL -->
            <table class="form-table" :class="selected.type !== 'DispelEffect' ? 'hidden' : ''">
                <tr>
                    <td>Behaviour Flags</td>
                    <td>
                        <flags v-model="selected.dispell_behaviour_flags" :flags="library.behaviourFlags"></flags>
                    </td>
                </tr>

                <tr>
                    <td>Status Flags</td>
                    <td>
                        <flags v-model="selected.dispell_behaviour_status_flags" :flags="library.behaviourStatusFlags"></flags>
                    </td>
                </tr>
            </table>

            <!-- ADD CURRENCY -->
            <table class="form-table" :class="selected.type !== 'AddCurrencyEffect' ? 'hidden' : ''">
                <tr>
                    <td>Type</td>
                    <td>
                        <selectpicker v-model="selected.currency_type" :options="library.currencyTypes"></selectpicker>
                    </td>
                </tr>

                <tr>
                    <td>Formula Text</td>
                    <td>
                        <textarea class="form-control" v-model="selected.currency_formula" style="font-family:monospace;"></textarea>
                    </td>
                </tr>
            </table>

            <!-- SEARCH DUMMIES -->
            <table class="form-table" :class="selected.type !== 'SearchDummiesEffect' ? 'hidden' : ''">
                <tr>
                    <td>Range</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.search_dummies_range">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Limit</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.search_dummies_limit">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Effect</td>
                    <td>
                        <div class="form-group">
                            <effect-field v-model="selected.search_dummies_effect_id" :effects="effects"></effect-field>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- MOVE EFFECT -->
            <table class="form-table" :class="selected.type !== 'MoveEffect' ? 'hidden' : ''">
                <tr>
                    <td>Animation</td>
                    <td>
                        <selectpicker v-model="selected.move_animation" :options="library.animations"></selectpicker>
                    </td>
                </tr>

                <tr>
                    <td>Speed</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.move_speed">
                    </td>
                </tr>

                <tr>
                    <td>Distance</td>
                    <td>
                        <input type="number" class="form-control" v-model="selected.move_distance">
                    </td>
                </tr>
            </table>

            <!-- SEARCH PERIMETER -->
            <table class="form-table" :class="selected.type !== 'SearchPerimeterEffect' ? 'hidden' : ''">
                <tr>
                    <td>Pick Random Side</td>
                    <td>
                        <div class="form-group">
                            <input type="checkbox" v-model="selected.search_perimeter_pick_random_side">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Sides</td>
                    <td>
                        <div class="form-group">
                            <flags v-model="selected.search_perimeter_sides" :flags="library.sides"></flags>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Limit</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.search_perimeter_limit">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Effect</td>
                    <td>
                        <div class="form-group">
                            <effect-field v-model="selected.search_perimeter_effect_id" :effects="effects"></effect-field>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="form-table" :class="['SearchAreaEffect', 'SearchPointsEffect', 'SearchCorpsesEffect'].includes(selected.type) ? '' : 'hidden'">
                <tr>
                    <td>Shape</td>
                    <td>
                        <selectpicker v-model="selected.search_area_shape" :options="library.shapes"></selectpicker>
                    </td>
                </tr>

                <tr>
                    <td>Exclude Origin</td>
                    <td>
                        <div class="form-group">
                            <input type="checkbox" v-model="selected.search_area_exclude_origin">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Exclude Target</td>
                    <td>
                        <div class="form-group">
                            <input type="checkbox" v-model="selected.search_area_exclude_target">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Check Line of Sight</td>
                    <td>
                        <div class="form-group">
                            <input type="checkbox" v-model="selected.search_area_check_line_of_sight">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Radius</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.search_area_radius">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Limit</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.search_area_limit">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Effect</td>
                    <td>
                        <div class="form-group">
                            <effect-field v-model="selected.search_area_effect_id" :effects="effects"></effect-field>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- SEARCH RANDOM POINTS -->
            <table class="form-table" :class="selected.type !== 'SearchRandomPoints' ? 'hidden' : ''">
                <tr>
                    <td>Range Min</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.search_random_points_range_min">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Range Max</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.search_random_points_range_max">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Limit</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.search_random_points_limit">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Effect</td>
                    <td>
                        <div class="form-group">
                            <effect-field v-model="selected.search_random_points_effect_id" :effects="effects"></effect-field>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- CREATE UNIT -->
            <table class="form-table" :class="selected.type !== 'CreateUnitEffect' ? 'hidden' : ''">
                <tr>
                    <td>Unit</td>
                    <td>
                        <div class="form-group">
                            <unit-field v-model="selected.create_unit_unit_id" :units="units"></unit-field>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Owner</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="selected.create_unit_owner"
                                          :nullable="true"
                                          :options="library.ownerTypes">
                            </selectpicker>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Health Fraction</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.create_unit_health_fraction">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Duration</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.create_unit_duration">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Kill on caster death</td>
                    <td>
                        <div class="form-group">
                            <input type="checkbox" v-model="selected.create_unit_kill_on_caster_death">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Kill on episode complete</td>
                    <td>
                        <div class="form-group">
                            <input type="checkbox" v-model="selected.create_unit_kill_on_episode_complete">
                        </div>
                    </td>
                </tr>
            </table>

            <!-- CREATE BEAM -->
            <table class="form-table" :class="selected.type !== 'CreateBeamEffect' ? 'hidden' : ''">
                <tr>
                    <td>Beam</td>
                    <td>
                        <div class="form-group">
                            <prefab-field v-model="selected.create_beam_path"></prefab-field>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- WAIT -->
            <table class="form-table" :class="selected.type !== 'WaitEffect' ? 'hidden' : ''">
                <tr>
                    <td>Seconds</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.wait_seconds">
                        </div>
                    </td>
                </tr>
            </table>

            <!-- HEAL FROM TARGET HEALTH -->
            <table class="form-table" :class="selected.type !== 'HealFromTargetHealthEffect' ? 'hidden' : ''">
                <tr>
                    <td>Fraction</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.heal_from_target_health_fraction">
                        </div>
                    </td>
                </tr>
            </table>

            <!-- RANDOM WAIT -->
            <table class="form-table" :class="selected.type !== 'RandomWaitEffect' ? 'hidden' : ''">
                <tr>
                    <td>Min</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.random_wait_min">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Max</td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control" v-model="selected.random_wait_max">
                        </div>
                    </td>
                </tr>
            </table>

            <table class="form-table" :class="['DamageEffect', 'WeaponDamageEffect', 'RandomElementDamageEffect', 'PerBehaviourStackDamageEffect', 'HealEffect', 'ShieldEffect'].includes(selected.type) ? '' : 'hidden'">
                <tr>
                    <td>Formula</td>
                    <td>
                        <selectpicker v-model="selected.formula_id"
                                      :nullable="true"
                                      :options="formulas"
                                      :valuecallback="(formula) => formula.id"
                                      :labelcallback="(formula) => formula.name">
                        </selectpicker>
                    </td>
                </tr>

                <tr>
                    <td>Formula Text</td>
                    <td>
                        <textarea class="form-control" v-model="selected.formula_text" style="font-family:monospace;"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                skills: [],
                effects: [],
                behaviours: [],
                properties: [],
                attributes: [],
                validators: [],
                rewards: [],
                units: [],
                formulas: [],
                items: [],
                missiles: [],
                library: window.library,
                selected: this.value
            };
        },

        props: ["value"],

        watch: {
            value: function (value) {
                this.selected = value;
            },
        },

        mounted() {
            window.axios.get('/frontend/effects').then(response => {
                this.effects = response.data;
            });

            window.axios.get('/frontend/units').then(response => {
                this.units = response.data;
            });

            window.axios.get('/frontend/rewards').then(response => {
                this.rewards = response.data;
            });

            window.axios.get('/frontend/skills').then(response => {
                this.skills = response.data;
            });

            window.axios.get('/frontend/behaviours').then(response => {
                this.behaviours = response.data;
            });

            window.axios.get('/frontend/missiles').then(response => {
                this.missiles = response.data;
            });

            window.axios.get('/frontend/validators').then(response => {
                this.validators = response.data;
            });

            window.axios.get('/frontend/attributes').then(response => {
                this.attributes = response.data;
            });

            window.axios.get('/frontend/properties').then(response => {
                this.properties = response.data;
            });

            window.axios.get('/frontend/missiles').then(response => {
                this.missiles = response.data;
            });

            window.axios.get('/frontend/items').then(response => {
                this.items = response.data;
            });

            window.axios.get('/frontend/formulas').then(response => {
                this.formulas = response.data;
            });
        },

        methods: {
            addAttachment: function (effect) {
                effect.attachments.push({
                    point: this.library.attachmentPoints[0]
                });
            },

            removeAttachment: function (effect, attachment) {
                let index = effect.attachments.indexOf(attachment);
                effect.attachments.splice(index, 1);
            },

            addEffect: function (item) {
                if (item.effects === undefined) {
                    item.effects = [];
                }

                item.effects.push({
                    id: this.effects[0] ? this.effects[0].id : undefined
                });
            },

            removeEffect: function (item, effect) {
                let index = item.effects.indexOf(effect);
                item.effects.splice(index, 1);
            },

            effectMoveUp: function (item, effect) {
                let index = item.effects.indexOf(effect);

                if (index === -1 || index === 0) {
                    return;
                }

                item.effects[index] = item.effects[index - 1];
                item.effects[index - 1] = effect;

                this.$forceUpdate();
            },

            effectMoveDown: function (item, effect) {
                let index = item.effects.indexOf(effect);

                if (index === -1 || index === item.effects.length) {
                    return;
                }

                item.effects[index] = item.effects[index + 1];
                item.effects[index + 1] = effect;

                this.$forceUpdate();
            },

            addValidator: function (item) {
                if (item.validators === undefined) {
                    Object.assign(item, {validators: []});
                }

                item.validators.push({
                    pivot: {
                        validator_id: this.validators[0] ? this.validators[0].id : undefined,
                        validator_purpose: library.validatorPurposes[0],
                    }
                });
            },

            removeValidator: function (item, validator) {
                let index = item.validators.indexOf(validator);
                item.validators.splice(index, 1);
            },

            validatorMoveUp: function (item, validator) {
                let index = item.validators.indexOf(validator);

                if (index === -1 || index === 0) {
                    return;
                }

                item.validators[index] = item.validators[index - 1];
                item.validators[index - 1] = validator;

                this.$forceUpdate();
            },

            validatorMoveDown: function (item, validator) {
                let index = item.validators.indexOf(validator);

                if (index === -1 || index === item.validators.length) {
                    return;
                }

                item.validators[index] = item.validators[index + 1];
                item.validators[index + 1] = validator;

                this.$forceUpdate();
            },
        }
    };
</script>

<style scoped>

</style>

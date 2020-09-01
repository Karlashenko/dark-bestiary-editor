<?php

declare(strict_types=1);

namespace App;

use App\Models\Achievement;
use App\Models\AchievementCondition;
use App\Models\Archetype;
use App\Models\Attachment;
use App\Models\AttributeModifier;
use App\Models\BehaviourTree;
use App\Models\Behaviour;
use App\Models\Currency;
use App\Models\Dialogue;
use App\Models\DialogueAction;
use App\Models\Effect;
use App\Models\Encounter;
use App\Models\Episode;
use App\Models\Food;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemRarity;
use App\Models\ItemType;
use App\Models\LootItem;
use App\Models\Missile;
use App\Models\Phrase;
use App\Models\Property;
use App\Models\Recipe;
use App\Models\Reward;
use App\Models\Scenario;
use App\Models\Scene;
use App\Models\Skill;
use App\Models\I18NVariable;
use App\Models\Skin;
use App\Models\Unit;
use App\Models\Validator;

/**
 * Class Library
 *
 * @package App
 */
class Library
{
    /**
     * @return array
     */
    public static function get(): array
    {
        $library = [
            'moverTypes'                => Missile::MOVER_TYPES,
            'lootItemTypes'             => LootItem::TYPES,
            'attachmentPoints'          => Attachment::POINTS,
            'entityTypes'               => I18NVariable::ENTITY_TYPES,
            'modifierTypes'             => AttributeModifier::TYPES,
            'behaviourTypes'            => Behaviour::TYPES,
            'behaviourFlags'            => Behaviour::FLAGS,
            'behaviourReApplyFlags'     => Behaviour::RE_APPLY_FLAGS,
            'behaviourStatusFlags'      => Behaviour::STATUS_FLAGS,
            'itemFlags'                 => Item::FLAGS,
            'ownerTypes'                => Effect::OWNER_TYPES,
            'itemRarityTypes'           => ItemRarity::TYPES,
            'equipmentStrategyTypes'    => ItemType::EQUIPMENT_STRATEGIES,
            'recipeCategories'          => Recipe::CATEGORIES,
            'itemSlotTypes'             => Item::SLOT_TYPES,
            'unitFlags'                 => Unit::FLAGS,
            'subjects'                  => ['Caster', 'Target'],
            'behaviourSubjects'         => ['Me', 'Other'],
            'effectTypes'               => Effect::TYPES,
            'effectTargetTypes'         => Effect::TARGET_TYPES,
            'damageTargets'             => Effect::DAMAGE_TARGETS,
            'damageTypes'               => Effect::DAMAGE_TYPES,
            'damageFlags'               => Effect::DAMAGE_FLAGS,
            'damageInfoFlags'           => Effect::DAMAGE_INFO_FLAGS,
            'healFlags'                 => Effect::HEAL_FLAGS,
            'rewardTypes'               => Reward::TYPES,
            'skillTargetTypes'          => Skill::TARGET_TYPES,
            'skillFlags'                => Skill::FLAGS,
            'skillTypes'                => Skill::TYPES,
            'resourceTypes'             => Skill::RESOURCE_TYPES,
            'currencyTypes'             => Currency::TYPES,
            'shapes'                    => Skill::SHAPES,
            'skinParts'                 => Skin::PARTS,
            'encounters'                => Encounter::TYPES,
            'entrances'                 => Scene::ENTRANCES,
            'achievementTypes'          => Achievement::TYPES,
            'achievementConditionTypes' => AchievementCondition::TYPES,
            'nodeGroups'                => array_keys(BehaviourTree::DICTIONARY),
            'nodeGroupsDictionary'      => BehaviourTree::DICTIONARY,
            'comparatorTypes'           => Validator::COMPARATORS,
            'validatorTypes'            => Validator::TYPES,
            'validatorPurposes'         => Validator::PURPOSES,
            'curveTypes'                => Archetype::CURVE_TYPES,
            'armorSoundTypes'           => Unit::ARMOR_SOUND_TYPES,
            'weaponSoundTypes'          => Effect::WEAPON_SOUND_TYPES,
            'scenarioTypes'             => Scenario::TYPES,
            'unitSourceTypes'           => Encounter::UNIT_SOURCE_TYPES,
            'narrators'                 => Dialogue::NARRATORS,
            'dialogueActionTypes'       => DialogueAction::TYPES,
            'foodTypes'                 => Food::TYPES,
            'sides'                     => self::getSides(),
            'icons'                     => self::getIcons(),
            'prefabs'                   => self::getPrefabs(),
            'equipmentMeshes'           => self::getEquipmentMeshes(),
            'animations'                => self::getAnimations(),
        ];

        return $library;
    }

    public static function getSides(): array
    {
        return [
            'Top',
            'Right',
            'Bottom',
            'Left',
        ];
    }

    /**
     * @return array
     */
    public static function getIcons(): array
    {
        $icons = [];

        $directory = new \RecursiveDirectoryIterator(\public_path('resources/Sprites/Icons'), \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);
        $directoryIterator = new \RecursiveIteratorIterator($directory);
        $regexIterator = new \RegexIterator($directoryIterator, '/^.+(.jpe?g|.png)$/i', \RecursiveRegexIterator::GET_MATCH);

        foreach ($regexIterator as $file) {
            $icon = \str_replace(\public_path(), '', $file[0]);

            if (\basename($icon)[0] === '.') {
                continue;
            }


            $unityPath = \str_replace(['resources/', $file[1]], '', $icon);
            $unityPath = \str_replace('\\', '', $unityPath);

            $icons[$unityPath] = [
                'url' => \str_replace('\\', '', $icon),
                'unityPath' => $unityPath,
                'extension' => $file[1],
            ];
        }

        return $icons;
    }

    public static function getAnimations(): array
    {
        return [
            'idle',
            'walk',
            'charge',
            'hit',
            'dig',
            'death',
            'stomp',
            'block',
            'drink',
            'slam',
            'mutilate',
            'double_shot',
            'dash_start',
            'dash_end',
            'leap',
            'leap_land',
            'dragon_breath',
            'dragon_flight_start',
            'dragon_flight_end',
            'throw',
            'kick',
            'attack',
            'attack_fist',
            'attack_shotgun',
            'attack_fist_main',
            'attack_fist_off',
            'attack_katar',
            'attack_katar_main',
            'attack_katar_off',
            'attack_claws',
            'attack_claws_main',
            'attack_claws_off',
            'attack_bow',
            'attack_dagger',
            'attack_dagger_main',
            'attack_dagger_off',
            'attack_fist',
            'attack_spear',
            'attack_spear_special',
            'attack_sword',
            'attack_sword_main',
            'attack_sword_off',
            'attack_great_sword',
            'attack_pistol',
            'attack_pistol_main',
            'attack_pistol_off',
            'attack_flamethrower_1h',
            'attack_flamethrower_1h_main',
            'attack_flamethrower_1h_off',
            'attack_crossbow',
            'attack_rifle',
            'attack_staff',
            'shield_slam',
            'rocket_jump_start',
            'rocket_jump_end',
            'channel',
            'cast',
            'cast_out',
            'cast_omni',
            'cast_directed',
        ];
    }

    /**
     * @return array
     */
    public static function getPrefabs(): array
    {
        $prefabs = [];

        $directory = new \RecursiveDirectoryIterator(\public_path('resources/Prefabs'), \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);
        $directoryIterator = new \RecursiveIteratorIterator($directory);
        $regexIterator = new \RegexIterator($directoryIterator, '/^.+(.prefab)$/i', \RecursiveRegexIterator::GET_MATCH);

        foreach ($regexIterator as $file) {
            $prefab = str_replace(\public_path(), '', $file[0]);

            if (\basename($prefab)[0] === '.') {
                continue;
            }

            $prefabs[] = \str_replace(['/resources/', '.prefab'], '', \str_replace('\\', '/', $prefab));
        }

        return $prefabs;
    }

    /**
     * @return array
     */
    public static function getEquipmentMeshes(): array
    {
        $prefabs = [];

        $directory = new \RecursiveDirectoryIterator(\public_path('resources/Meshes'), \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);
        $directoryIterator = new \RecursiveIteratorIterator($directory);
        $regexIterator = new \RegexIterator($directoryIterator, '/^.+(.asset)$/i', \RecursiveRegexIterator::GET_MATCH);

        foreach ($regexIterator as $file) {
            $prefab = str_replace(\public_path(), '', $file[0]);

            if (\basename($prefab)[0] === '.') {
                continue;
            }

            $prefabs[] = \str_replace(['/resources/', '.asset'], '', \str_replace('\\', '/', $prefab));
        }

        return $prefabs;
    }
}

<?php

namespace App\Http\Controllers\Api\AliasGroup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alias\StoreAliasGroupRequest;
use App\Models\AliasGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;

class AliasGroupController extends Controller
{
    public function index()
    {
        $groups = AliasGroup::where('user_id', user()->id)
            ->withCount('aliases')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $groups]);
    }

    public function store(StoreAliasGroupRequest $request)
    {
        $group = AliasGroup::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json(['data' => $group->loadCount('aliases')], 201);
    }

    public function update(Request $request, string $id)
    {
        $group = AliasGroup::where('user_id', user()->id)->where('id', $id)->firstOrFail();

        $request->validate([
            'name' => [
                'nullable',
                'string',
                'max:80',
                Rule::unique('alias_groups', 'name')->where('user_id', user()->id)->ignore($group->id),
            ],
            'description' => 'nullable|string|max:200',
            'color' => [
                'nullable',
                'string',
                Rule::in(AliasGroup::PALETTE),
            ],
            'sort_order' => 'nullable|integer|min:0|max:1000',
        ]);

        $group->fill($request->only(['name', 'description', 'color', 'sort_order']));
        $group->save();

        return response()->json(['data' => $group->loadCount('aliases')]);
    }

    public function destroy(string $id)
    {
        $group = AliasGroup::where('user_id', user()->id)
            ->where('id', $id)
            ->withCount('aliases')
            ->firstOrFail();

        if ($group->aliases_count > 0) {
            return response()->json([
                'message' => "Group \"{$group->name}\" still has {$group->aliases_count} alias(es). Move them out first.",
            ], 422);
        }

        $group->delete();

        return response()->noContent();
    }
}

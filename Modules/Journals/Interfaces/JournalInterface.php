<?php

namespace Modules\Journals\Interfaces;

interface JournalInterface
{
    public function all();
    public function active();
    public function forSchool($schoolId);
    public function getPaginateAll();
    public function search($request);
    public function store($request);
    public function show($id);
    public function update($request, $id);
    public function destroy($id);
    public function close($id);
}
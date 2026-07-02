<?php

namespace App\Traits\Tests;

use Illuminate\Support\Facades\DB;

trait ClearsTestTables
{
    protected function clearVotes(): void
    {
        DB::table('votes')->delete();
    }

    protected function clearComments(): void
    {
        DB::table('comments')->delete();
    }

    protected function clearStatistics(): void
    {
        DB::table('statistics')->delete();
    }

    protected function clearQuestionSuggestions(): void
    {
        DB::table('question_company_suggestions')->delete();
        DB::table('question_position_suggestions')->delete();
    }

    protected function clearQuestionPivots(): void
    {
        DB::table('company_question')->delete();
        DB::table('position_question')->delete();
    }

    protected function clearQuestionMorphPivots(): void
    {
        DB::table('taggables')->delete();
        DB::table('gradables')->delete();
    }

    /**
     * Removes questions and all rows that reference them via foreign keys.
     */
    protected function clearQuestionsAndDependencies(): void
    {
        $this->clearVotes();
        $this->clearComments();
        $this->clearStatistics();
        $this->clearQuestionSuggestions();
        $this->clearQuestionMorphPivots();
        $this->clearQuestionPivots();
        DB::table('questions')->delete();
    }

    protected function clearCompaniesAndDependencies(): void
    {
        DB::table('question_company_suggestions')->delete();
        DB::table('company_question')->delete();
        $this->clearStatistics();
        DB::table('companies')->delete();
    }

    protected function clearPositionsAndDependencies(): void
    {
        DB::table('question_position_suggestions')->delete();
        DB::table('position_question')->delete();
        $this->clearStatistics();
        DB::table('positions')->delete();
    }

    protected function clearTagsAndDependencies(): void
    {
        DB::table('taggables')->delete();
        DB::table('tags')->delete();
    }

    /**
     * Clears questions and related taxonomy tables used by propose/statistics flows.
     */
    protected function clearQuestionDomainTables(): void
    {
        $this->clearQuestionsAndDependencies();
        DB::table('companies')->delete();
        DB::table('positions')->delete();
        DB::table('tags')->delete();
    }
}

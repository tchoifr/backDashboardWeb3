<?php

namespace App\Mapper;

use App\DTO\Dashboard\ProjectCardDTO;
use App\Entity\Project;
use DateTimeInterface;

class ProjectMapper
{
    public function toCardDTO(Project $project): ProjectCardDTO
    {
        return new ProjectCardDTO(
            id: $project->getId() ?? 0,
            title: $project->getTitle(),
            companyName: $project->getCompanyName(),
            amountUsdc: $project->getAmountUsdc(),
            status: $project->getStatus(),
            deadline: $this->formatDate($project->getDeadline()),
        );
    }

    private function formatDate(?DateTimeInterface $date): ?string
    {
        return $date?->format(DateTimeInterface::ATOM);
    }
}

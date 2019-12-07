<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 */
class Location implements DisplayableInterface
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     */
    private $parentLocation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Box", mappedBy="location")
     */
    private $boxes;

    public function __construct()
    {
        $this->boxes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection|Box[]
     */
    public function getBoxes(): Collection
    {
        return $this->boxes;
    }

    public function addBox(Box $box): self
    {
        if (!$this->boxes->contains($box)) {
            $this->boxes[] = $box;
            $box->setLocation($this);
        }

        return $this;
    }

    public function removeBox(Box $box): self
    {
        if ($this->boxes->contains($box)) {
            $this->boxes->removeElement($box);
            // set the owning side to null (unless already changed)
            if ($box->getLocation() === $this) {
                $box->setLocation(null);
            }
        }

        return $this;
    }

    public function getParentLocation(): ?self
    {
        return $this->parentLocation;
    }

    public function setParentLocation(?self $parentLocation): self
    {
        $hold = $this->parentLocation;

        $this->parentLocation = $parentLocation;

        try {
            // check for recursion and bail if we find it
            iterator_to_array($this->parentWalker());
        } catch (\Exception $e) {
            $this->parentLocation = $hold;
            throw $e;
        }

        return $this;
    }

    /**
     * Generator that walks back through parents and yields each generation in
     * turn.
     *
     * Throws an exception if the same ID appears twice.
     *
     * @return Location[]
     */
    protected function parentWalker(): iterable
    {
        $ids = [];

        $location = $this;
        while ($location !== null) {
            if (in_array($location->getId(), $ids)) {
                throw new \Exception('Recursive location hierarchy found');
            }
            $ids[] = $location->getId();

            yield $location;
            $location = $location->getParentLocation();
        }
    }

    /**
     * Generates the display label for this class, showing its full place on
     * the tree.
     *
     * @return string A full display label for this location.  For instance
     *                "Home - Garage - Wire Rack".
     */
    public function getDisplayLabel()
    {
        $build = [];
        foreach ($this->parentWalker() as $location) {
            $build[] = $location->getLabel();
        }

        return implode(' - ', array_reverse($build));
    }
}

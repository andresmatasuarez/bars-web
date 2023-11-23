import { useCallback, useMemo, useState } from "react";
import { MovieSections } from "../../types";
import { Props as ReactSelectProps, SingleValue } from "react-select";

export type SectionOptionShape = { value: string; label: string };
export type ChangeSectionHandler = NonNullable<
  ReactSelectProps<SectionOptionShape, false>["onChange"]
>;
export type SectionOption = SingleValue<SectionOptionShape>;

export const DEFAULT_SECTION_ALL = {
  value: "all",
  label: "Todas las secciones",
} as const satisfies SectionOptionShape;

export default function useSectionSelector({
  movieSections,
}: {
  movieSections: MovieSections;
}): {
  selectedSection: SectionOption;
  sectionOptions: SectionOptionShape[];
  changeSection: ChangeSectionHandler;
} {
  const [selectedSection, setSelectedSection] =
    useState<SectionOption>(DEFAULT_SECTION_ALL);

  const sectionOptions = useMemo((): SectionOptionShape[] => {
    return [
      DEFAULT_SECTION_ALL,
      ...Object.entries(movieSections).map(([sectionId, sectionLabel]) => ({
        value: sectionId,
        label: sectionLabel,
      })),
    ];
  }, [movieSections]);

  const changeSection = useCallback<ChangeSectionHandler>((selectedOption) => {
    setSelectedSection(selectedOption ?? DEFAULT_SECTION_ALL);
  }, []);

  return { selectedSection, changeSection, sectionOptions };
}

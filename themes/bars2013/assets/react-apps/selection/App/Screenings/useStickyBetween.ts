import { RefObject, useEffect, useRef, useState } from 'react';

export default function useStickyBetween(offsetTop: number = 0): {
  afterTop: boolean;
  afterBottom: boolean;
  topSensorRef: RefObject<HTMLDivElement>;
  bottomSensorRef: RefObject<HTMLDivElement>;
  stickyRef: RefObject<HTMLDivElement>;
} {
  const topSensorRef = useRef<HTMLDivElement>(null);
  const bottomSensorRef = useRef<HTMLDivElement>(null);
  const stickyRef = useRef<HTMLDivElement>(null);

  const [afterTop, setAfterTop] = useState(false);
  const [afterBottom, setAfterBottom] = useState(false);

  useEffect(() => {
    if (!topSensorRef.current || !bottomSensorRef.current || !stickyRef.current) {
      return;
    }

    const windowObserverTop = new IntersectionObserver(
      ([entry]) => {
        setAfterTop(
          !entry.isIntersecting &&
            !!entry.rootBounds &&
            entry.boundingClientRect.y <= entry.rootBounds.height,
        );
      },
      {
        root: null, // null implies window
        rootMargin: `${-offsetTop}px 0px 0px 0px`,
      },
    );

    const windowObserverBottom = new IntersectionObserver(
      ([entry]) => {
        setAfterBottom(
          afterTop &&
            !entry.isIntersecting &&
            !!stickyRef.current &&
            entry.boundingClientRect.y <= stickyRef.current.offsetHeight + offsetTop,
        );
      },
      {
        root: null, // null implies window
        rootMargin: `${-stickyRef.current.offsetHeight - offsetTop}px 0px 0px 0px`,
      },
    );

    windowObserverTop.observe(topSensorRef.current);
    windowObserverBottom.observe(bottomSensorRef.current);

    return () => {
      windowObserverTop?.disconnect();
      windowObserverBottom?.disconnect();
    };
  }, [afterTop]);

  return {
    afterTop,
    afterBottom,
    topSensorRef,
    bottomSensorRef,
    stickyRef,
  };
}

# Eventsystem

## Description

This is a pattern that can be used for implementing a Eventsystem that has a 
smaller footprint than using Decorators for notify other classes on for example 
updates to DataObjects.

## Context of Use

This should be used when you wishes to decentralize changes that propagates 
through the system.

The use case that created this pattern was related to a static cache system where
there were a master method looking for onAfter* events on Page and then told the
static caching functionality what pages that should be recached.

This can be avoided by letting individual controllers to listening on specific 
updates in the system and then taking appropriate actions based on what event was
raised and what the event included.

## How It Works

@todo: write documentation

## Specifications

@todo: write documentation

## Changelog

v0.1 - Initial working implementation 
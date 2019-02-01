# `components/`

A component is essentially a package that is used by [services](/services/README.md). Components should have low dependencies themselves so components can move around from one wpkickstart framework to another.

But some components may also use other components, in which you may have to move components that share dependencies around together. In these cases it may be good to consider combining them into a single component if need-be.
